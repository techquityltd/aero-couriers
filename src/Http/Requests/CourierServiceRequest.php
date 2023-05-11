<?php

namespace Techquity\Aero\Couriers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourierServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'name' => 'max:255',
            'group' => 'max:255'
        ];
    }
}
