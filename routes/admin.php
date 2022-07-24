<?php

use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Http\Request;
use Techquity\Aero\Couriers\FulfillmentInstallation;
use Techquity\Aero\Couriers\FulfillmentMethodInstallation;
use Techquity\Aero\Couriers\Http\Controllers\FulfillmentController;

Route::prefix('courier/configuration/')->name('courier.configuration.')->group(function () {
    Route::post('fulfillment-method/{driver?}/{fulfillmentMethod?}', function (string $driver, ?FulfillmentMethod $fulfillmentMethod = null) {
        return FulfillmentMethodInstallation::loadSettingsView($driver, $fulfillmentMethod);
    })->name('fulfillment-method');
    Route::post('fulfillment/{fulfillmentMethod?}/{fulfillment?}', function (?FulfillmentMethod $fulfillmentMethod = null, ?Fulfillment $fulfillment = null) {
        return FulfillmentInstallation::loadSettingsView($fulfillmentMethod, $fulfillment);
    })->name('fulfillment');
});

Route::get('courier/consignments/{fulfillment?}', function (Request $request, ?Fulfillment $fulfillment = null) {

    $q = strtolower($request->input('q'));

    return Fulfillment::query()
        ->select('id', 'reference')
        ->whereLower('reference', 'like', "%{$q}%")
        ->limit(20)
        ->cursor()
        ->map(function ($fulfillment) {
            return [
                'value' => (string) $fulfillment->id,
                'name' => $fulfillment->reference,
            ];
        });
})->name('courier.consignments');

Route::prefix('courier/')->name('admin.courier.')->group(function () {
    Route::delete('fulfillment/{fulfillment}', [FulfillmentController::class, 'destroy'])->name('fulfillment.delete');
});
