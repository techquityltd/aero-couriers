<?php

namespace Techquity\Aero\Couriers\Http\Responses\Steps;

use Aero\Responses\ResponseBuilder;
use Aero\Responses\ResponseStep;

class SaveShippingMethodCourierConfiguration implements ResponseStep
{
    public function handle(ResponseBuilder $builder, \Closure $next)
    {
        $shippingMethod = $builder->method;
        $shippingMethod->courier_configuration = json_encode($builder->request->input('configuration'));
        $shippingMethod->save();

        return $next($builder);
    }
}
