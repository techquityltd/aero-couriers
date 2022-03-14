<?php

use Techquity\Aero\Couriers\CouriersManager;

Route::post('courier/options/{driver?}', function ($driver = null) {
    if (!$driver) return;

    $driver = app(CouriersManager::class)->driver($driver);

    dd(app($driver)->setup()->options());

    return view('courier.fulfillments::fulfillment-driver-setup', compact('options'))->render();
})->name('courier.fulfillments.setup');
