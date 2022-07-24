<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\AdminSlot;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentStore;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentUpdate;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Aero\Responses\ResponseBuilder;
use Illuminate\Http\Request;

class FulfillmentInstallation extends AbstractCourierInstallation
{
    /**
     * Register the required macros
     */
    protected static function configureMacros(): void
    {
        // Register the get mutator for courier configuration...
        Fulfillment::macro('getCourierConfigurationAttribute', function ($value) {
            /* @var $this \Aero\Fulfillment\Models\Fulfillment */
            return $this->courierConfig = $this->courierConfig ?: new FulfillmentConfiguration($this->method, $this, json_decode($value, true));
        });

        // Register the set mutator for courier configuration...
        Fulfillment::macro('setCourierConfigurationAttribute', function (array $value) {
            /* @var $this \Aero\Fulfillment\Models\Fulfillment */
            $this->attributes['courier_configuration'] = json_encode($value);
        });
    }

    /**
     * Register the admin slots for Fulfillments.
     */
    protected static function configureAdminSlots(): void
    {
        AdminSlot::inject('orders.fulfillment.edit.cards', 'courier::fulfillments.configuration');
        AdminSlot::inject('orders.fulfillment.new.cards', 'courier::fulfillments.configuration');

        AdminSlot::inject('orders.fulfillment.edit.cards', 'courier::fulfillments.consignments');
        AdminSlot::inject('orders.fulfillment.new.cards', 'courier::fulfillments.consignments');

        AdminSlot::inject('orders.fulfillment.edit.extra.sidebar', 'courier::fulfillments.information');
        AdminSlot::inject('orders.fulfillment.new.extra.sidebar', 'courier::fulfillments.information');
    }

    /**
     * Configure the requests for creating and updating a fulfillment method.
     */
    protected static function configureAdminRequests(): void
    {
        // Validate the configuration...
        AdminOrderFulfillmentStore::middleware(static::validateFulfillmentConfiguration());
        AdminOrderFulfillmentUpdate::middleware(static::validateFulfillmentConfiguration());

        // Save the configuration...
        AdminOrderFulfillmentStore::extend(static::saveFulfillmentConfiguration());
        AdminOrderFulfillmentUpdate::extend(static::saveFulfillmentConfiguration());
    }

    protected static function modelObservers()
    {
        // Delete the configuration...
        FulfillmentMethod::deleted(function (FulfillmentMethod $fulfillmentMethod) {
            $configuration = new CourierConfiguration($fulfillmentMethod->driver, $fulfillmentMethod);
            if ($configuration->isValid()) {
                $configuration->delete();
            }
        });
    }

    /**
     * Validate the courier configuration settings.
     */
    protected static function validateFulfillmentConfiguration(): callable
    {
        return (function (Request $request, \Closure $next) {
            $configuration = new FulfillmentConfiguration(
                FulfillmentMethod::findOrFail($request->input('fulfillment_method'))
            );

            if ($configuration->hasCourierConfiguration()) {
                $validator = $configuration->validator($request);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            return $next($request);
        });
    }

    /**
     * Save the fulfillment configuration settings.
     */
    protected static function saveFulfillmentConfiguration(): callable
    {
        return (function (ResponseBuilder $builder) {
            $configuration = new FulfillmentConfiguration($builder->fulfillment->method, $builder->fulfillment);

            if ($configuration->hasCourierConfiguration()) {
                $configuration->save($builder->request);
            }
        });
    }

    /**
     * Get the settings view form.
     */
    public static function loadSettingsView(?FulfillmentMethod $fulfillmentMethod, ?Fulfillment $fulfillment)
    {
        if (isset($fulfillment)) {
            $configuration = $fulfillment->courier_configuration;
        } else {
            $configuration = new FulfillmentConfiguration($fulfillmentMethod, $fulfillment);
        }

        if ($configuration->hasCourierConfiguration()) {
            $data['configuration'] = [
                'key' => $configuration->key(),
                'group' => $configuration->group(),
                'settings' => $configuration->settings()
            ];

            return view('courier::configuration', $data)->render();
        }
    }
}
