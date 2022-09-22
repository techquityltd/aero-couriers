<?php

namespace Techquity\Aero\Couriers\Traits;

use Aero\Responses\ResponseBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Techquity\Aero\Couriers\CourierDriver;

trait InteractsWithFulfillmentDriver
{
    public static function getCourierDrivers(?string $driver = null)
    {
        $drivers = collect(Relation::$morphMap)->filter(function ($relation) {
            return is_subclass_of($relation, CourierDriver::class);
        });

        return $driver ? $drivers->get($driver) : $drivers;
    }

    public static function attachCourierDrivers(ResponseBuilder $builder): void
    {
        $builder->setData('courierDrivers', static::getCourierDrivers()->keys()->toArray());
    }
}
