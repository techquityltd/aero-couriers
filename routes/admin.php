<?php


use Aero\Fulfillment\FulfillmentProcessor;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Techquity\Aero\Couriers\CourierConfiguration;

Route::post('courier/configuration/{courier?}/{method?}', function ($courier, FulfillmentMethod $method = null) {
    $configuration = new CourierConfiguration($courier, $method);

    if ($configuration->isValid()) {
        $data['configuration'] = [
            'key' => $configuration->key(),
            'group' => $configuration->group(),
            'settings' => $configuration->settings()
        ];

        return view('courier::configuration', $data)->render();
    }
})->name('courier.fulfillment-method-config');

Route::post('courier/fulfillment/{method?}/{fulfillment?}', function (FulfillmentMethod $method, Fulfillment $fulfillment = null) {
    $configuration = new CourierConfiguration($method->driver, $method);

    if ($configuration->isValid()) {
        $data['configuration'] = [
            'key' => $configuration->key(),
            'group' => $configuration->group(),
            'settings' => $configuration->settings()
        ];

        return view('courier::configuration', $data)->render();
    }
})->name('courier.fulfillment-config');
