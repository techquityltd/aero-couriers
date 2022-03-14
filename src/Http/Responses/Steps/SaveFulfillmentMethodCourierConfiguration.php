<?php

namespace Techquity\Aero\Couriers\Http\Responses\Steps;

use Aero\Responses\ResponseBuilder;
use Aero\Responses\ResponseStep;

class SaveFulfillmentMethodCourierConfiguration implements ResponseStep
{
    public function handle(ResponseBuilder $builder, \Closure $next)
    {
        $fulfillmentMethod = $builder->fulfillmentMethod;

        $fulfillmentMethod->courier = $builder->request->input('courier');
        $fulfillmentMethod->courier_configuration = json_encode($builder->request->input('configuration'));
        $fulfillmentMethod->save();

        return $next($builder);
    }
}
