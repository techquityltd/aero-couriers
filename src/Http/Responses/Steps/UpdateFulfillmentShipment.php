<?php

namespace Techquity\Aero\Couriers\Http\Responses\Steps;

use Aero\Responses\ResponseBuilder;
use Aero\Responses\ResponseStep;

class UpdateFulfillmentShipment implements ResponseStep
{
    public function handle(ResponseBuilder $builder, \Closure $next)
    {
        $fulfillment = $builder->fulfillment;

        if (!isset($fulfillment->method) && $fulfillment->method->isCourier || !$fulfillment->courierShipment) {
            return $next($builder);
        }

        $fulfillment->courierShipment->courierService()->associate($builder->request->service);
        $fulfillment->courierShipment->courierConnector()->associate($builder->request->connector);
        $fulfillment->courierShipment->save();

        return $next($builder);
    }
}
