<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\AdminSlot;
use Aero\Admin\Http\Responses\Configuration\AdminFulfillmentMethodStore;
use Aero\Admin\Http\Responses\Configuration\AdminFulfillmentMethodUpdate;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Aero\Responses\ResponseBuilder;
use Illuminate\Http\Request;

class FulfillmentMethodInstallation extends AbstractCourierInstallation
{
    /**
     * Register the required macros
     */
    protected static function configureMacros(): void
    {
        // Register the get mutator for courier configuration...
        FulfillmentMethod::macro('getCourierConfigurationAttribute', function () {
            /* @var $this \Aero\Fulfillment\Models\FulfillmentMethod */
            return $this->courierConfig = $this->courierConfig ?: new FulfillmentMethodConfiguration($this->driver, $this);
        });

        // Register the set mutator for courier configuration...
        FulfillmentMethod::macro('setCourierConfigurationAttribute', function ($value) {
            /* @var $this \Aero\Fulfillment\Models\FulfillmentMethod */
            $this->attributes['courier_configuration'] = json_encode($value->toArray());
        });
    }

    /**
     * Register the admin slots for Fulfillment Methods.
     */
    protected static function configureAdminSlots(): void
    {
        AdminSlot::inject('configuration.fulfillment-methods.new.cards', 'courier::fulfillment-methods.configuration');
        AdminSlot::inject('configuration.fulfillment-methods.edit.cards', 'courier::fulfillment-methods.configuration');
    }

    /**
     * Configure the requests for creating and updating a fulfillment method.
     */
    protected static function configureAdminRequests(): void
    {
        // Validate the configuration...
        AdminFulfillmentMethodStore::middleware(static::validateFulfillmentMethodConfiguration());
        AdminFulfillmentMethodUpdate::middleware(static::validateFulfillmentMethodConfiguration());

        // Save the configuration...
        AdminFulfillmentMethodStore::extend(static::saveFulfillmentMethodConfiguration());
        AdminFulfillmentMethodUpdate::extend(static::saveFulfillmentMethodConfiguration());
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
    protected static function validateFulfillmentMethodConfiguration(): callable
    {
        return (function (Request $request, \Closure $next) {
            $configuration = new FulfillmentMethodConfiguration($request->input('driver'));

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
    protected static function saveFulfillmentMethodConfiguration(): callable
    {
        return (function (ResponseBuilder $builder) {
            $configuration = new FulfillmentMethodConfiguration($builder->request->input('driver'), $builder->fulfillmentMethod);

            if ($configuration->hasCourierConfiguration()) {
                $configuration->save($builder->request);
            }
        });
    }

    /**
     * Get the settings view form.
     */
    public static function loadSettingsView(string $driver, ?FulfillmentMethod $fulfillmentMethod)
    {
        $configuration = new FulfillmentMethodConfiguration($driver, $fulfillmentMethod);

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
