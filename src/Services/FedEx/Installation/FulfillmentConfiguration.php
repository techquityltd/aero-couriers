<?php

namespace Techquity\Aero\Couriers\Services\FedEx\Installation;

class FulfillmentConfiguration
{
    /**
     * Get the configuration options.
     */
    public function types(): array
    {
        return [
            'parcels' => 'special.parcels',
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
