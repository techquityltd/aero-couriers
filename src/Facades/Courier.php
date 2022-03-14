<?php

namespace Techquity\Aero\Couriers\Facades;

use Illuminate\Support\Facades\Facade;

class Courier extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'courier';
    }
}
