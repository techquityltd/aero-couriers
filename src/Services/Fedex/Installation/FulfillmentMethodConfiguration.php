<?php

namespace Techquity\Aero\Couriers\Services\FedEx\Installation;

class FulfillmentMethodConfiguration
{
    /**
     * Get the configuration options.
     */
    public function types(): array
    {
        return [
            'api_key' => 'encrypted',
            'secret_key' => 'encrypted',
            'server' => [
                'select' => ['sandbox' => 'Sandbox', 'production' => 'Production']
            ],
            'account_number' => 'string',
            'company' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'string',
            'phone' => 'string',
            'line_1' => 'string',
            'line_2' => 'string',
            'line_3' => 'string',
            'city' => 'string',
            'zone_name' => 'string',
            'postcode' => 'string',
            'country_code' => 'string',
            'residential' => 'boolean',
        ];
    }

    /**
     * Get the configuration validation rules.
     */
    public function rules(): array
    {
        return [
            'api_key' => 'required|max:255',
            'secret_key' => 'required|max:255',
            'account_number' => 'required|max:255',
            'company' => 'required|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255',
            'phone' => 'required|max:255',
            'line_1' => 'required|max:255',
            'line_2' => 'max:255',
            'line_3' => 'max:255',
            'city' => 'required|max:255',
            'zone_name' => 'max:255',
            'postcode' => 'required|max:255',
            'country_code' => 'required|max:255',
            'residential' => 'boolean|max:255',
        ];
    }
}
