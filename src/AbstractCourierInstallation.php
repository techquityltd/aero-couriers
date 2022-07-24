<?php

namespace Techquity\Aero\Couriers;

abstract class AbstractCourierInstallation
{
    public static function setup()
    {
        // Configure any required macros...
        static::configureMacros();

        // Configure any required admin slots...
        static::configureAdminSlots();

        // Configure the incoming requests
        static::configureAdminRequests();


        // static::modelObservers();
    }
}
