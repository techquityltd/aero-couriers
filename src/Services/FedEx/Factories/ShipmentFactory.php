<?php

namespace Techquity\Aero\Couriers\Services\FedEx\Factories;

use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Support\Arr;
use Techquity\Aero\Couriers\Services\FedEx\FedExDriver;

class ShipmentFactory
{
    protected Fulfillment $fulfillment;
    protected array $configuration;

    public function __construct(Fulfillment $fulfillment, array $configuration)
    {
        $this->fulfillment = $fulfillment;
        $this->configuration = $configuration;
    }

    public function make(): array
    {
        return [
            'requestedShipment' => [
                'shipDatestamp' => $this->fulfillment->created_at->format('Y-m-d'),
                'shipper' => $this->getShipper(),
                'recipients' => $this->getRecipients(),
                'pickupType' => $this->configuration['pickup_type'],
                'serviceType' => $this->configuration['service_type'],
                'packagingType' => $this->configuration['packaging_type'],
                'totalWeight' => $this->getTotalWeight(),
                'shippingChargesPayment' => $this->getShippingChargesPayment(),
                'blockInsightVisibility' => false,
                'labelSpecification' => $this->getLabelSpecification(),
                'rateRequestType' => $this->getRateRequestType(),
                'preferredCurrency' => 'UKL',
                'totalPackageCount' => 1,
                'requestedPackageLineItems' => $this->getRequestedPackageLineItems(),
            ],
            'labelResponseOptions' => 'LABEL',
            'accountNumber' => [
                'value' => $this->configuration['account_number'],
            ],
        ];
    }

    /**
     * Get the Shipper contact details for this shipment.
     */
    protected function getShipper(): array
    {
        return [
            'address' => [
                'streetLines' => array_values(array_filter(Arr::only($this->configuration, ['line_1', 'line_2', 'line_3']))),
                'city' => $this->configuration['city'],
                'postalCode' => $this->configuration['postcode'],
                'countryCode' => $this->configuration['country_code'],
                'residential' => $this->configuration['residential'] ?? false,
            ],
            'contact' => [
                'personName' => "{$this->configuration['first_name']} {$this->configuration['last_name']}",
                'companyName' => $this->configuration['company'],
                'emailAddress' => $this->configuration['email'],
                'phoneNumber' => $this->configuration['phone'],
            ]
        ];
    }

    /**
     * The descriptive data for the recipient location to which the shipment is to be received.
     */
    protected function getRecipients(): array
    {
        return [
            [
                'address' => [
                    'streetLines' => array_filter([$this->fulfillment->address->line_1, $this->fulfillment->address->line_2]),
                    'city' => $this->fulfillment->address->city,
                    //'stateOrProvinceCode' => $this->fulfillment->address->zone->code
                    'postalCode' => $this->fulfillment->address->postcode,
                    'countryCode' => $this->fulfillment->address->country_code,
                    //'residential' => $this->fulfillment->address['residential'],
                ],
                'contact' => [
                    'personName' => $this->fulfillment->address->fullName,
                    'companyName' => $this->fulfillment->address->company,
                    'emailAddress' => $this->fulfillment->email,
                    'phoneNumber' => $this->fulfillment->mobile,
                ],
                'deliveryInstructions' => (string) $this->fulfillment->delivery_note
            ]
        ];
    }

    /**
     * Get the total weight of the shipment.
     */
    protected function getTotalWeight(): float
    {
        return collect($this->fulfillment->courierConfig('parcels.weights', FedExDriver::NAME, [
            1
        ]))->sum();
    }

    /**
     * Specifies the payment details specifying the method and means of payment to FedEx.
     */
    protected function getShippingChargesPayment(): array
    {
        return [
            'paymentType' => $this->configuration['payment_type'],
            'payor' => [
                'responsibleParty' => array_merge($this->getShipper(), [
                    'accountNumber' => [
                        'value' => $this->configuration['account_number']
                    ]
                ])
            ]
        ];
    }

    protected function getLabelSpecification(): array
    {
        return [
            'labelFormatType' => 'COMMON2D',
            'labelOrder' => 'SHIPPING_LABEL_FIRST',
            'labelStockType' => $this->configuration['label_stock_type'],
            'imageType' => 'PDF',
        ];
    }

    protected function getRateRequestType(): array
    {
        return ['LIST'];
    }

    protected function getRequestedPackageLineItems(): array
    {
        $this->fulfillment->update([
            'state' => 'open'
        ]);

        return collect($this->fulfillment->courierConfig('parcels.weights', FedExDriver::NAME, [
            1
        ]))->map(function ($weight) {
            return [
                'weight' => [
                    'units' => 'KG',
                    'value' => (float) $weight,
                    'imageType' => 'PDF',
                ]
            ];
        })->values()->toArray();
    }
}
