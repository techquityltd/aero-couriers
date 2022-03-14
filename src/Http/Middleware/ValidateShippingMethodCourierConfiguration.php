<?php

namespace Techquity\Aero\Couriers\Http\Middleware;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Techquity\Aero\Couriers\Facades\Courier;

class ValidateShippingMethodCourierConfiguration
{
    /**
     * Validate the request if courier was selected as the driver.
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($request->request->get('driver') === 'courier') {

            $courier = $request->request->get('courier');

            Validator::make(
                $this->getData($request, $courier),
                $this->getRules($courier)
            )->validate();
        }

        return $next($request);
    }

    /**
     * Get the rules from the couriers shipping method setup.
     */
    protected function getRules(string $courier): array
    {
        return array_merge([
            'courier' =>  ['required', Rule::in(array_keys(Courier::getDrivers()))]
        ], Courier::getShippingMethodConfiguration()[$courier]->rules() ?? []);
    }

    /**
     * Get the courier configuration data to be validated
     */
    protected function getData(Request $request, string $courier): array
    {
        return array_merge([
            'courier' => $request->request->get('courier')
        ], data_get($request->request->get('configuration'), $courier, []));
    }
}
