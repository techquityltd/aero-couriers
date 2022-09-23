<?php

use Illuminate\Support\Facades\Route;
use Techquity\Aero\Couriers\Http\Controllers\CourierConnectorsController;
use Techquity\Aero\Couriers\Http\Controllers\CourierServicesController;
use Techquity\Aero\Couriers\Http\Controllers\CourierShipmentsController;
use Techquity\Aero\Couriers\Http\Controllers\CourierCollectionsController;

Route::prefix('/courier/shipments')->middleware('can:couriers.manage-shipments')->name('admin.courier-manager.shipments.')->group(function () {
    Route::get('/', [CourierShipmentsController::class, 'index'])->name('index');
    Route::put('/commit/{fulfillment}', [CourierShipmentsController::class, 'commit'])->name('commit');
    Route::put('/collect/{fulfillment}', [CourierShipmentsController::class, 'collect'])->name('collect');
    Route::post('/print/{shipment}', [CourierShipmentsController::class, 'print'])->name('print');
    Route::delete('/delete/{fulfillment}', [CourierShipmentsController::class, 'delete'])->name('delete');
    Route::get('/request/{shipment}', [CourierShipmentsController::class, 'request'])->name('request');
    Route::get('/response/{shipment}', [CourierShipmentsController::class, 'response'])->name('response');
});

Route::prefix('/courier/connectors')->middleware('can:couriers.manage-connectors')->name('admin.courier-manager.connectors.')->group(function () {
    Route::get('/', [CourierConnectorsController::class, 'index'])->name('index');
    Route::post('/', [CourierConnectorsController::class, 'store'])->name('store');
    Route::get('/{connector}', [CourierConnectorsController::class, 'index'])->name('edit');
    Route::put('/{connector}', [CourierConnectorsController::class, 'update'])->name('update');
});

Route::prefix('/courier/services')->middleware('can:couriers.manage-services')->name('admin.courier-manager.services.')->group(function () {
    Route::get('/', [CourierServicesController::class, 'index'])->name('index');
    Route::get('/{service}', [CourierServicesController::class, 'index'])->name('edit');
    Route::put('/{service}', [CourierServicesController::class, 'update'])->name('update');
    Route::post('/refresh', [CourierServicesController::class, 'store'])->name('store')->middleware('throttle:60,1');
});

Route::prefix('/courier/collections')->middleware('can:couriers.manage-collections')->name('admin.courier-manager.collections.')->group(function () {
    Route::get('/', [CourierCollectionsController::class, 'index'])->name('index');
    Route::post('/manifest/{collection}', [CourierCollectionsController::class, 'manifest'])->name('manifest');
    Route::delete('/delete/{collection}', [CourierCollectionsController::class, 'delete'])->name('delete');
});
