<?php

namespace Techquity\Aero\Couriers\Services\FedEx;

use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Client\Response;
use Techquity\Aero\Couriers\Models\FulfillmentLog;
use Techquity\Aero\Couriers\Services\AbstractCourierDriver;
use Techquity\Aero\Couriers\Services\FedEx\Client\Client;
use Techquity\Aero\Couriers\Services\FedEx\Factories\ShipmentFactory;
use Techquity\Aero\Couriers\Services\FedEx\Installation\FulfillmentMethodConfiguration;
use Techquity\Aero\Couriers\Services\FedEx\Installation\ShippingMethodConfiguration;
use Techquity\Aero\OrderDocuments\Models\OrderDocumentGroup;
use Illuminate\Support\Str;
use Techquity\Aero\Couriers\Services\FedEx\Installation\FulfillmentConfiguration;

class FedExDriver extends AbstractCourierDriver
{
    /**
     * The name of the fulfillment driver.
     */
    public const NAME = 'fedex';

    /**
     * Create a new fulfillment method setup instance.
     */
    public function fulfillmentMethodConfiguration()
    {
        return new FulfillmentMethodConfiguration();
    }

    /**
     * Create a new shipping method setup instance.
     */
    public function shippingMethodConfiguration()
    {
        return new ShippingMethodConfiguration();
    }

    /**
     * Create a new fulfillment setup instance.
     */
    public function fulfillmentConfiguration()
    {
        return new FulfillmentConfiguration();
    }

    /**
     * Create a new consignment
     */
    public function createConsignment()
    {
        $shipment = new ShipmentFactory($this->fulfillment, $this->configuration);

        return (new Client($this->configuration))
            ->post('ship/v1/shipments', ['json' => $shipment->make()])
            ->onFailure(function (Response $response) {
                collect($response->get('errors'))->each(function ($error) use ($response) {
                    $this->fulfillment->logs()->create([
                        'type' => FulfillmentLog::ERROR,
                        'title' => $error->code,
                        'message' => $error->message,
                        'data' => ['code' => $response->get('code')]
                    ]);
                });

                $this->fulfillment->state = Fulfillment::FAILED;
                $this->fulfillment->save();

                return $response;
            })
            ->onSuccessful(function (Response $response) {
                $trackingCode = $response->get('output.transactionShipments.0.masterTrackingNumber');

                $this->fulfillment->tracking_code = $trackingCode;
                $this->fulfillment->tracking_url = $this->getTrackingUrl($trackingCode);
                $this->fulfillment->state = 'pushed';
                $this->fulfillment->save();

                $this->fulfillment->logs()->create([
                    'type' => 'pushed',
                    'title' => $response->get('transactionId'),
                    'message' => 'Shipment successfully created',
                    'data' => ['code' => $response->get('code')]
                ]);

                $this->generateDocumentsLabels($response);
            });
    }

    /**
     * Cancel an existing consignment
     */
    public function cancelConsignment()
    {
        // Only send cancel request if the fulfillment was processed
        if ($this->fulfillment->tracking_url) {
            (new Client($this->configuration))
                ->put('ship/v1/shipments/cancel', ['json' => [
                    'accountNumber' => [
                        'value' => $this->configuration['account_number'],
                    ],
                    'trackingNumber' => $this->fulfillment->tracking_url,
                    'deletionControl' => 'DELETE_ALL_PACKAGES',
                ]])
                ->onSuccessful(function ($response) {
                    $this->fulfillment->state = Fulfillment::CANCELED;
                    $this->fulfillment->tracking_code = null;
                    $this->fulfillment->tracking_url = null;
                    $this->fulfillment->save();

                    $this->fulfillment->logs()->create([
                        'type' => FulfillmentLog::INFO,
                        'title' => $response->get('transactionId'),
                        'message' => 'Shipment successfully cancelled',
                    ]);
                })
                ->onFailure(function ($response) {
                    $this->fulfillment->logs()->create([
                        'type' => FulfillmentLog::ERROR,
                        'title' => $response->get('transactionId'),
                        'message' => 'Shipment failed to cancel',
                        'data' => ['code' => $response->get('errors')]
                    ]);
                    $this->fulfillment->state = Fulfillment::FAILED;
                    $this->fulfillment->save();
                });
        } else {
            $this->fulfillment->state = Fulfillment::CANCELED;
            $this->fulfillment->save();
        }
    }

    /**
     * Get the tracking url.
     */
    protected function getTrackingUrl(string $trackingCode): string
    {
        return "https://www.fedex.com/fedextrack?tracknumbers={$trackingCode}&cntry_code={$this->fulfillment->address->country_code}";
    }

    /**
     * Generate a pdf label and attach to the order.
     */
    protected function generateDocumentsLabels(Response $response): void
    {
        $group = orderDocumentGroup::create([
            'key' => 'fedex_labels_' . Str::random(8)
        ]);

        $labels = collect($response->get('output.transactionShipments.0.pieceResponses'));

        $labels->each(function ($label) use ($group) {
            $orderDocument = $this->order()->documents()->firstOrCreate([
                'key' => sprintf(
                    "label_fedex_%s_%s",
                    $label->packageSequenceNumber ?? 1,
                    $this->fulfillment->reference
                ),
            ], [
                'group_id' => $group->id,
            ]);
            $orderDocument->createBase64Document(
                data_get($label, 'packageDocuments.0.encodedLabel')
            );
        });

        $group->update([
            'complete' => true
        ]);
    }
}