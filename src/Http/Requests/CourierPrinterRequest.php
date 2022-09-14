<?php

namespace Techquity\Aero\Couriers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourierPrinterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('couriers.manage-printers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'host' => 'required|max:255',
            'port' => 'required|integer',
        ];
    }
}
