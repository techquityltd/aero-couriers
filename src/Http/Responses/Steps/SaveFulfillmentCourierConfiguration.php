<?php

namespace Techquity\Aero\Couriers\Http\Responses\Steps;

use Aero\Responses\ResponseBuilder;
use Aero\Responses\ResponseStep;

class SaveFulfillmentCourierConfiguration implements ResponseStep
{
    public function handle(ResponseBuilder $builder, \Closure $next)
    {
        $fulfillment = $builder->fulfillment;

        if (!$fulfillment) {
            $fulfillment = $builder->order->fulfillments()->latest()->first();
        }

        $fulfillment->courier_configuration = json_encode($builder->request->input('configuration'));

        $fulfillment->save();

        return $next($builder);
    }
}
