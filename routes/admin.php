<?php

use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Techquity\Aero\Couriers\FulfillmentInstallation;
use Techquity\Aero\Couriers\FulfillmentMethodInstallation;

Route::prefix('courier/configuration/')->name('courier.configuration.')->group(function () {
    Route::any('fulfillment-method/{driver?}/{fulfillmentMethod?}', function (string $driver, ?FulfillmentMethod $fulfillmentMethod = null) {
        return FulfillmentMethodInstallation::loadSettingsView($driver, $fulfillmentMethod);
    })->name('fulfillment-method');
    Route::any('fulfillment/{fulfillmentMethod?}/{fulfillment?}', function (?FulfillmentMethod $fulfillmentMethod = null, ?Fulfillment $fulfillment = null) {
        return FulfillmentInstallation::loadSettingsView($fulfillmentMethod, $fulfillment);
    })->name('fulfillment');
});
