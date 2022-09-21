<?php

namespace Techquity\Aero\Couriers\Http\Responses\Steps;

use Aero\Responses\ResponseBuilder;
use Aero\Responses\ResponseStep;
use Techquity\Aero\Couriers\Actions\CreateShipment;
use Techquity\Aero\Couriers\Models\CourierConnector;
use Techquity\Aero\Couriers\Models\CourierService;

class CreateAndAttachShipment implements ResponseStep
{
    public function handle(ResponseBuilder $builder, \Closure $next)
    {
        if (!isset($builder->fulfillment->method) || !$builder->fulfillment->method->isCourier) {
            return $next($builder);
        }

        $fulfillment = $builder->fulfillment;

        (new CreateShipment($fulfillment))
            ->usingService(CourierService::findOrFail($builder->request->service))
            ->usingConnector(CourierConnector::findOrFail($builder->request->connector));

        return $next($builder);
    }
}
