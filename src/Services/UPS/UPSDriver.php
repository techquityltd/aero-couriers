<?php

namespace Techquity\Aero\Couriers\Services\Ups;

use Aero\Common\Facades\Settings;
use Aero\Common\Models\Country;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Client\Response;
use Techquity\Aero\Couriers\Models\FulfillmentLog;
use Techquity\Aero\Couriers\Services\Ups\Client\Client;
use Techquity\Aero\Couriers\Services\Ups\Factories\ShipmentFactory;
use Techquity\Aero\OrderDocuments\Models\OrderDocumentGroup;
use Illuminate\Support\Str;
use Techquity\Aero\Couriers\Services\AbstractCourierDriver;
use Techquity\Aero\Couriers\Services\UPS\References\PackageTypes;
use Techquity\Aero\Couriers\Services\UPS\References\ServiceCodes;
use Techquity\Aero\Couriers\Services\UPS\References\ShipmentChargeType;

class UpsDriver extends AbstractCourierDriver
{
    /**
     * The name of the fulfillment driver.
     */
    public const NAME = 'UPS';

    public const KEY = '_ups';

    /**
     * Create a new consignment
     */
    public function createConsignment()
    {
        $shipment = new ShipmentFactory($this->fulfillment, $this->configuration);

        $this->fulfillment->update(['state' => 'open']);

        return (new Client($this->configuration))
            ->post('ship/v1/shipments', ['json' => $shipment->make()])
            ->onFailure(function (Response $response) use ($shipment) {
                foreach ($response->get('response.errors') as $error) {
                    $this->fulfillment->logs()->create([
                        'type' => FulfillmentLog::ERROR,
                        'title' => $error->code,
                        'message' => $error->message,
                        'data' => ['code' => $response->get('response')]
                    ]);
                }

                $this->fulfillment->state = Fulfillment::FAILED;
                $this->fulfillment->save();

                return $response;
            })
            ->onSuccessful(function (Response $response) {
                $this->fulfillment->update(['state' => 'open']);
                dd($response, $response->string());
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
                'key' => "label_fedex_{$label->packageSequenceNumber}_{$this->fulfillment->reference}",
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

    public static function fulfillmentMethodSettings(SettingGroup $group)
    {
        $group->string('ups_service_code')
            ->hint('UPS delivery service_code')
            ->required()
            ->default(ServiceCodes::DEFAULT)
            ->in(ServiceCodes::TYPES);

        $group->string('shipment_charge_type')
            ->hint('UPS delivery service_code')
            ->required()
            ->default(ShipmentChargeType::DEFAULT)
            ->in(ShipmentChargeType::TYPES);

        $group->string('package_type')
            ->hint('UPS delivery service_code')
            ->required()
            ->default(PackageTypes::DEFAULT)
            ->in(PackageTypes::TYPES);

        $group->integer('default_length')
            ->hint('Default parcel length (cm)')
            ->max(100)
            ->default(15);

        $group->integer('default_width')
            ->hint('Default parcel width (cm)')
            ->default(15);

        $group->integer('default_height')
            ->hint('Default parcel height (cm)')
            ->default(15);
    }

    /**
     * Configure the core UPS settings.
     */
    public static function courierSettings(): void
    {
        Settings::group('courier_ups', function (SettingGroup $group) {
            $group->string('server')
                ->hint('The current environment')
                ->required()
                ->in(['sandbox' => 'Sandbox', 'production' => 'Production']);

            $group->string('account_number')
                ->hint('UPS Associate 6 digit account number')
                ->required()
                ->size(6);

            $group->encrypted('access_key')
                ->hint('UPS Associate 6 digit account number')
                ->required()
                ->size(16);

            $group->string('username')
                ->hint('UPS Customers MyUPS Username')
                ->required()
                ->max(30);

            $group->encrypted('password')
                ->hint('UPS Customers MyUPS Password')
                ->required()
                ->max(26);

            $group->string('transaction_source')
                ->hint('Identifies the client/source application that is calling')
                ->default('Aero Commerce')
                ->required()
                ->max(26);

            $group->string('company')
                ->hint('Shippers company name')
                ->required()
                ->max(30);

            $group->string('person_name')
                ->hint('Shippers Attention Name')
                ->required()
                ->max(30);

            $group->string('tax_identification_number')
                ->hint('Shipper’s Tax Identification Number')
                ->max(15);

            $group->string('phone_number')
                ->hint('Shipper’s phone Number without extension')
                ->required()
                ->max(15);

            $group->string('phone_extension')
                ->hint('Shipper’s phone extension')
                ->max(15);

            $group->string('address_line_1')
                ->hint('Shipper’s address line 1')
                ->required()
                ->max(30);

            $group->string('address_line_2')
                ->hint('Shipper’s address line 1 (not required)')
                ->max(30);

            $group->string('address_line_3')
                ->hint('Shipper’s address line 1 (not required)')
                ->max(30);

            $group->string('city')
                ->hint('Shipper’s city')
                ->required()
                ->max(30);

            $group->string('state_province_code')
                ->hint('Shippers state or province code.')
                ->min(2)
                ->max(5);

            $group->string('postal_code')
                ->hint('Shipper’s postcode')
                ->required()
                ->max(9);

            $group->eloquent('country', Country::class)
                ->hint('Shipper’s postcode')
                ->required();
        });
    }
}
