<?php

namespace Techquity\Aero\Couriers;

use Aero\Common\Settings\SettingGroup;
use Illuminate\Support\Collection;
use Aero\Fulfillment\FulfillmentProcessor;

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
