<?php

namespace Techquity\Aero\Couriers\Services\UPS\Factories;

use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Services\UPS\References\ServiceCodes;

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
            'ShipmentRequest' => [
                'Shipment' => [
                    'ReferenceNumber' => [
                        'Value' => $this->fulfillment->reference,
                    ],
                    'Description' => $this->fulfillment->items->first()->order->reference,
                    // 'ReturnService' => [
                    //     'Code' => '', // ReturnServiceCode
                    // ],
                    //'DocumentsOnlyIndicator' => true,
                    'Shipper' => $this->getShipper(),
                    'ShipTo' => $this->getRecipient(),
                    'PaymentInformation' => [
                        'ShipmentCharge' => [
                            'Type' => $this->configuration['shipment_charge_type'],
                            'BillShipper' => [
                                'AccountNumber' => $this->configuration['account_number'],
                            ]
                        ]
                    ],
                    'Service' => [
                        'Code' => $this->configuration['service_code'],
                        'Description' => ServiceCodes::TYPES[$this->configuration['service_code']]
                    ],
                    'Package' => [
                        'Packaging' => [
                            'Code' =>  $this->configuration['package_type']
                        ],
                        'Dimensions' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'CM'
                            ],
                            'Length' => '50',
                            'Width' => '50',
                            'Height' => '50',
                        ],
                        'PackageWeight' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'KGS'
                            ],
                            'Weight' => '10'
                        ]

                    ],
                    'LabelSpecification' => [
                        'LabelImageFormat' => [
                            'Code' => 'PNG',
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function getShipper(): array
    {
        return [
            'Name' => $this->configuration['company'],
            'AttentionName' => $this->configuration['person_name'],
            'TaxIdentificationNumber' => $this->configuration['tax_identification_number'] ?? '',
            'Phone' => [
                'Number' => $this->configuration['phone_number'],
                'Extension' => $this->configuration['phone_extension']
            ],
            'ShipperNumber' => $this->configuration['account_number'],
            'Address' => [
                'AddressLine' => $this->configuration['address_line'],
                'City' => $this->configuration['city'],
                'StateProvinceCode' => $this->configuration['state_province_code'] ?? '',
                'PostalCode' => $this->configuration['postal_code'],
                'CountryCode' => $this->configuration['country_code'],
            ],
        ];
    }

    protected function getRecipient(): array
    {
        return [
            'Name' => $this->fulfillment->address->company ?? $this->fulfillment->address->fullName,
            'AttentionName' => $this->fulfillment->address->fullName,
            'Phone' => [
                'Number' => $this->fulfillment->mobile
            ],
            'EMailAddress' => $this->fulfillment->email,
            'Address' => [
                'AddressLine' => array_filter([$this->fulfillment->address->line_1, $this->fulfillment->address->line_2]),
                'City' => $this->fulfillment->address->city,
                'StateProvinceCode' => optional($this->fulfillment->address->zone)->code ?? '',
                'PostalCode' => $this->fulfillment->address->postcode,
                'CountryCode' => $this->fulfillment->address->country_code,
            ],
        ];
    }
}
