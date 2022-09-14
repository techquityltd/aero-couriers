<?php

namespace Techquity\Aero\Couriers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourierConnectorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('couriers.manage-connectors');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('courier_connectors', 'name')->ignore($this->connector),
            ],
            'carrier' => 'required',
            'url' => 'nullable|active_url',
            'user' => 'nullable|max:255',
            'password' => 'nullable|max:255',
            'token' => 'nullable|max:255',
        ];
    }
}
