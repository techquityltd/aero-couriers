<?php

namespace Techquity\Aero\Couriers\Services\FedEx\Installation;

use Techquity\Aero\Couriers\Services\FedEx\References\LabelStockTypes;
use Techquity\Aero\Couriers\Services\FedEx\References\PackagingTypes;
use Techquity\Aero\Couriers\Services\FedEx\References\PaymentTypes;
use Techquity\Aero\Couriers\Services\FedEx\References\PickupTypes;
use Techquity\Aero\Couriers\Services\FedEx\References\ServiceTypes;

class ShippingMethodConfiguration
{
    /**
     * Get the configuration options.
     */
    public function types(): array
    {
        return [
            'pickup_type' => [
                'select' => PickupTypes::TYPES
            ],
            'service_type' => [
                'select' => ServiceTypes::TYPES
            ],
            'packaging_type' => [
                'select' => PackagingTypes::TYPES
            ],
            'payment_type' => [
                'select' => PaymentTypes::TYPES
            ],
            'label_stock_type' => [
                'select' => LabelStockTypes::TYPES
            ],
        ];
    }

    /**
     * Get the configuration validation rules.
     */
    public function rules(): array
    {
        return [];
    }
}
