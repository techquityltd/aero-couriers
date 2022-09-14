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

        $this->shipment->courierService()->associate($builder->request->service);
        $this->shipment->courierConnector()->associate($builder->request->connector);
        $this->shipment->courierPrinter()->associate($builder->request->printer);
        $this->shipment->save();

        return $next($builder);
    }
}
