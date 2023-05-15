<?php

namespace Techquity\Aero\Couriers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourierServiceGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('couriers.manage-services');
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
                Rule::unique('courier_service_groups', 'name')->ignore($this->group),
            ],
            'sort' => 'nullable|numeric',
        ];
    }
}
