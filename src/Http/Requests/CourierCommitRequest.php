<?php

namespace Techquity\Aero\Couriers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourierCommitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('couriers.manage-shipments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'connector' => 'exists:gfs_connectors,id',
            'carrier' => 'exists:gfs_services,carrier',
        ];
    }
}
