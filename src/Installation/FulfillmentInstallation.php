<?php

namespace Techquity\Aero\Couriers\Installation;

use Aero\Admin\AdminSlot;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentCreatePage;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentEditPage;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentStore;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentUpdate;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Aero\Responses\ResponseBuilder;
use Illuminate\Http\Request;
use Techquity\Aero\Couriers\Abstracts\AbstractCourierInstallation;

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

        // Register the parent consignment relationship...
        Fulfillment::macro('parent', function () {
            /* @var $this \Aero\Fulfillment\Models\Fulfillment */
            return $this->belongsTo(Fulfillment::class, 'parent_id');
        });

        // Register the parent consignment relationship...
        Fulfillment::macro('children', function () {
            /* @var $this \Aero\Fulfillment\Models\Fulfillment */
            return $this->hasMany(Fulfillment::class, 'parent_id');
        });
    }

    /**
     * Register the admin slots for Fulfillments.
     */
    protected static function configureAdminSlots(): void
    {
        AdminOrderFulfillmentCreatePage::extend(function ($page) {
            $page->setData('parent', Fulfillment::find($page->request->query('parent')));
            $page->setData('sisters', $page->getData('order')->fulfillments);

            // Add additional courier information...
            AdminSlot::inject('orders.fulfillment.new.sidebar', 'courier::fulfillments.information-new');
        });

        AdminOrderFulfillmentEditPage::extend(function ($page) {
            $fulfillment = $page->getData('fulfillment');

            $page->setData('parent', $fulfillment->parent);
            $page->setData('sisters', data_get($fulfillment, 'items.0.order.fulfillments')->where('id', '!=', $fulfillment->id));

            // Add additional courier information...
            AdminSlot::inject('orders.fulfillment.edit.extra.sidebar', 'courier::fulfillments.information-edit');
        });

        // Allow client to delete, process or retry fulfillments...
        AdminSlot::inject('orders.fulfillment.edit.cards.extra.top', 'courier::fulfillments.manage');
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

            // Only validate if no parent
            if ($request->input('parent')) {
                $parent = Fulfillment::findOrFail($request->input('parent'));
                // Child consignments cannot be a parent...
                if ($parent->parent) {
                    return back()->withInput();
                }
            } else {
                if ($configuration->hasCourierConfiguration()) {
                    $validator = $configuration->validator($request);

                    if ($validator->fails()) {
                        return back()->withErrors($validator)->withInput();
                    }
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
            if ($parent = $builder->request->input('parent')) {
                $parent = Fulfillment::findOrFail($parent);

                $builder->fulfillment->parent()->associate($parent);
                $builder->fulfillment->save();
            } else {
                $configuration = new FulfillmentConfiguration($builder->fulfillment->method, $builder->fulfillment);

                if ($configuration->hasCourierConfiguration()) {
                    $configuration->save($builder->request);
                }
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
