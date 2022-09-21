<?php

namespace Techquity\Aero\Couriers\Http\Responses\Steps;

use Aero\Responses\ResponseBuilder;
use Aero\Responses\ResponseStep;
use Techquity\Aero\Couriers\CourierDriver;

class SaveFulfillmentMethodCourierOptions implements ResponseStep
{
    public function handle(ResponseBuilder $builder, \Closure $next)
    {
        $fulfillmentMethod = $builder->fulfillmentMethod;

        if ($fulfillmentMethod->getDriver() instanceof CourierDriver) {
            $fulfillmentMethod->courierService()->associate($builder->request->input('service'))->save();
            $fulfillmentMethod->courierConnector()->associate($builder->request->input('connector'))->save();
        } else {
            $fulfillmentMethod->courierService()->dissociate()->save();
            $fulfillmentMethod->courierConnector()->dissociate()->save();
        }

        return $next($builder);
    }
}
