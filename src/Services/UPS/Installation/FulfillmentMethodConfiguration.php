<?php

namespace Techquity\Aero\Couriers\Services\UPS\Installation;

class FulfillmentMethodConfiguration
{
    /**
     * Get the configuration options.
     */
    public function types(): array
    {
        return [
            'account_number' => 'string',
            'access_key' => 'encrypted',
            'username' => 'string',
            'password' => 'encrypted',
            'transaction_source' => 'string',
            'server' => [
                'select' => ['sandbox' => 'Sandbox', 'production' => 'Production']
            ],
            'company' => 'string',
            'person_name' => 'string',
            'tax_identification_number' => 'string',
            'phone_number' => 'string',
            'phone_extension' => 'string',
            'address_line' => 'string',
            'city' => 'string',
            'state_province_code' => 'string',
            'postal_code' => 'string',
            'country_code' => 'string',
        ];
    }

    /**
     * Get the configuration validation rules.
     */
    public function rules(): array
    {
        return [
            'account_number' => 'required',
            'transaction_source' => 'max:255',
            'access_key' => 'required|max:255',
            'username' => 'required|max:255',
            'password' => 'required|max:255',
            'server' => 'required|in:sandbox,production',
            'company' => 'required|max:30',
            'person_name' => 'required|max:30',
            'tax_identification_number' => 'max:15',
            'phone_number' => 'required|max:9',
            'phone_extension' => 'max:4',
            'address_line' => 'required|max:35',
            'city' => 'required|max:30',
            'state_province_code' => 'max:5',
            'postal_code' => 'required|max:9',
            'country_code' => 'required|max:2',
        ];
    }
}
