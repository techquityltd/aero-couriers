<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available Couriers
    |--------------------------------------------------------------------------
    |
    | Here you can configure the couriers that are available to the store.
    | This package can be used without couriers offering some enhancements to
    | to the fulfillment system.
    |
    | Available Drivers: "fedex", "ups"
    */
    'drivers' => [
        //Techquity\Aero\Couriers\Services\FedEx\FedExDriver::class,
        Techquity\Aero\Couriers\Services\Ups\UpsDriver::class
    ],
];
