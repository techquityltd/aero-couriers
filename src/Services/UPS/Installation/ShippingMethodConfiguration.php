<?php

namespace Techquity\Aero\Couriers\Services\UPS\Installation;

use Techquity\Aero\Couriers\Services\UPS\References\PackageTypes;
use Techquity\Aero\Couriers\Services\UPS\References\ServiceCodes;
use Techquity\Aero\Couriers\Services\UPS\References\ShipmentChargeType;

class ShippingMethodConfiguration
{
    /**
     * Get the configuration options.
     */
    public function types(): array
    {
        return [
            'service_code' => [
                'select' => ServiceCodes::TYPES
            ],
            'shipment_charge_type' => [
                'select' => ShipmentChargeType::TYPES
            ],
            'package_type' => [
                'select' => PackageTypes::TYPES
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
