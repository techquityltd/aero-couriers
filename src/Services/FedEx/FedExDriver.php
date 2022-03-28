<?php

namespace Techquity\Aero\Couriers\Services\FedEx;

use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Client\Response;
use Techquity\Aero\Couriers\Models\FulfillmentLog;
use Techquity\Aero\Couriers\Services\AbstractCourierDriver;
use Techquity\Aero\Couriers\Services\Fedex\Client\Client;
use Techquity\Aero\Couriers\Services\Fedex\Factories\ShipmentFactory;
use Techquity\Aero\Couriers\Services\FedEx\Installation\FulfillmentMethodConfiguration;
use Techquity\Aero\Couriers\Services\FedEx\Installation\ShippingMethodConfiguration;
use Techquity\Aero\OrderDocuments\Models\OrderDocumentGroup;
use Illuminate\Support\Str;

class FedExDriver extends AbstractCourierDriver
{
    /**
     * The name of the fulfillment driver.
     */
    public const NAME = 'fedex';

    /**
     * Create a new fulfillment setup instance.
     */
    public function fulfillmentMethodConfiguration()
    {
        return new FulfillmentMethodConfiguration();
    }

    /**
     * Create a new fulfillment setup instance.
     */
    public function shippingMethodConfiguration()
    {
        return new ShippingMethodConfiguration();
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

                return $response;
            })
            ->onSuccessful(function (Response $response) {
                $trackingCode = $response->get('output.transactionShipments.0.masterTrackingNumber');

                $this->fulfillment->tracking_code = $trackingCode;
                $this->fulfillment->tracking_url = $this->getTrackingUrl($trackingCode);
                $this->fulfillment->state = Fulfillment::SUCCESSFUL;
                $this->fulfillment->save();

                $this->fulfillment->logs()->create([
                    'type' => FulfillmentLog::SUCCESS,
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
                        'value' => 'Your account number'
                    ],
                    'trackingNumber' => $this->fulfillment->tracking_url,
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
                });
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

        $orderDocument = $this->order()->documents()->firstOrCreate([
            'key' => 'label_fedex_' . $this->fulfillment->reference,
        ], [
            'group_id' => $group->id,
        ]);

        $orderDocument->createBase64Document(
            $response->get('output.transactionShipments.0.pieceResponses.0.packageDocuments.0.encodedLabel')
        );
    }
}
