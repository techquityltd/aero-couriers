<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\AdminSlot;
use Aero\Admin\BulkAction;
use Aero\Admin\Http\Responses\Configuration\AdminFulfillmentMethodStore;
use Aero\Admin\Http\Responses\Configuration\AdminFulfillmentMethodUpdate;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentStore;
use Aero\Admin\Http\Responses\Orders\AdminOrderFulfillmentUpdate;
use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\ShippingMethod;
use Aero\Common\Facades\Settings;
use Aero\Common\Providers\ModuleServiceProvider;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Database\Eloquent\Relations\Relation;
use Aero\Responses\ResponseBuilder;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Techquity\Aero\Couriers\BulkActions\CancelFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\CreateFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\DeleteFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\DispatchOrdersBulkAction;
use Techquity\Aero\Couriers\BulkActions\DownloadLabelsBulkAction;
use Techquity\Aero\Couriers\BulkActions\PrintShippingLabelsBulkAction;
use Techquity\Aero\Couriers\Http\Middleware\ValidateFulfillmentCourierConfiguration;
use Techquity\Aero\Couriers\Http\Responses\Steps\SaveFulfillmentCourierConfiguration;
use Techquity\Aero\Couriers\Models\FulfillmentLog;

class CouriersServiceProvider extends ModuleServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/couriers.php', 'couriers');
    }

    /**
     * Bootstrap the application services.
     */
    public function setup()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        Router::addAdminRoutes(__DIR__ . '/../routes/admin.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/admin/views', 'courier');

        // Register the courier drivers...
        Relation::morphMap(
            collect(config('couriers.drivers'))->mapWithKeys(fn ($driver) => [$driver::NAME => $driver])->toArray()
        );

        // Register the required application settings...
        $this->configureCourierSettings();

        // Extend and configure fulfillment methods...
        $this->configureFulfillmentMethods();

        // Extend and configure fulfillments...
        $this->configureFulfillments();
    }

    /**
     *  Register the application and courier settings.
     */
    protected function configureCourierSettings(): void
    {
        // Global courier settings...
        Settings::group('courier', function (SettingGroup $group) {
            $group->boolean('automatic_process')
                ->label('Process fulfillments automatically after creating fulfillments.')
                ->default(false);

            $group->string('default_weight')->in(['g', 'kg'])->default('g');
        });

        // Settings unique to the courier...
        collect(config('couriers.drivers'))->each(fn ($driver) => $driver::courierSettings());

        Fulfillment::macro('getCourierConfigurationAttribute', function ($value) {
            if (!$this->courierModelConfiguration) {
                $this->courierModelConfiguration = new CourierModelConfiguration(collect(json_decode($value)), $this);
            }

            return $this->courierModelConfiguration;
        });
        Fulfillment::macro('setCourierConfigurationAttribute', function ($value) {
            $this->attributes['courier_configuration'] = json_encode($value->toArray());
        });
    }

    /**
     * Configure fulfillment methods to allow custom configuration.
     */
    protected function configureFulfillmentMethods(): void
    {
        AdminSlot::inject('configuration.fulfillment-methods.new.cards', 'courier::fulfillment-methods.configuration');
        AdminSlot::inject('configuration.fulfillment-methods.edit.cards', 'courier::fulfillment-methods.configuration');

        // Validate the configuration...
        AdminFulfillmentMethodStore::middleware(function (Request $request, \Closure $next) {
            $configuration = new CourierConfiguration($request->input('driver'));

            if ($configuration->isValid()) {
                $validator = $configuration->validate();

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            return $next($request);
        });

        AdminFulfillmentMethodUpdate::middleware(function (Request $request, \Closure $next) {
            $configuration = new CourierConfiguration($request->input('driver'));

            if ($configuration->isValid()) {
                $validator = $configuration->validate();

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            return $next($request);
        });

        // Save the configuration...
        AdminFulfillmentMethodStore::extend(function (ResponseBuilder $builder) {
            $configuration = new CourierConfiguration($builder->request->input('driver'), $builder->fulfillmentMethod);
            if ($configuration->isValid()) {
                $configuration->save();
            }
        });

        AdminFulfillmentMethodUpdate::extend(function (ResponseBuilder $builder) {
            $configuration = new CourierConfiguration($builder->request->input('driver'), $builder->fulfillmentMethod);
            if ($configuration->isValid()) {
                $configuration->save();
            }
        });

        // Delete the configuration...
        FulfillmentMethod::deleted(function (FulfillmentMethod $fulfillmentMethod) {
            $configuration = new CourierConfiguration($fulfillmentMethod->driver, $fulfillmentMethod);
            if ($configuration->isValid()) {
                $configuration->delete();
            }
        });
    }

    protected function configureFulfillments(): void
    {
        // Add required macros and attributes...
        Fulfillment::makeFillable('courier_configuration');

        AdminSlot::inject('orders.fulfillment.edit.cards', 'courier::fulfillments.configuration');
        AdminSlot::inject('orders.fulfillment.new.cards', 'courier::fulfillments.configuration');

        AdminOrderFulfillmentStore::middleware(function (Request $request, \Closure $next) {
            $fulfillmentMethod = FulfillmentMethod::find($request->input('fulfillment_method'));

            $configuration = new CourierConfiguration($fulfillmentMethod->driver, $fulfillmentMethod);

            if ($configuration->isValid()) {
                $validator = $configuration->validate();

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            return $next($request);
        });

        AdminOrderFulfillmentUpdate::middleware(function (Request $request, \Closure $next) {
            $fulfillmentMethod = FulfillmentMethod::find($request->input('fulfillment_method'));

            $configuration = new CourierConfiguration($fulfillmentMethod->driver, $fulfillmentMethod);

            if ($configuration->isValid()) {
                $validator = $configuration->validate();

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            return $next($request);
        });

        AdminOrderFulfillmentStore::extend(function (ResponseBuilder $builder) {
            $configuration = new CourierConfiguration($builder->fulfillment->method->driver, $builder->fulfillment->method, $builder->fulfillment);

            if ($configuration->isValid()) {
                $configuration->save();
            }
        });

        AdminOrderFulfillmentUpdate::extend(function (ResponseBuilder $builder) {
            $configuration = new CourierConfiguration($builder->fulfillment->method->driver, $builder->fulfillment->method, $builder->fulfillment);

            if ($configuration->isValid()) {
                $configuration->save();
            }
        });
    }



    protected function addLoggingToFulfillments()
    {
        Fulfillment::macro('logs', function () {
            return $this->hasMany(FulfillmentLog::class)->orderByDesc('id');
        });

        AdminSlot::inject('orders.fulfillment.edit.cards', 'courier::fulfillments.logs');
    }

    /**
     * Configure the applications bulk actions.
     */
    protected function configureBulkActions(): void
    {
        BulkAction::create(DispatchOrdersBulkAction::class, OrdersResourceList::class)
            ->title('Dispatch Fulfillments')
            ->permissions('orders.edit');

        BulkAction::create(CreateFulfillmentsBulkAction::class, OrdersResourceList::class)
            ->title('Create Fulfillments')
            ->permissions('orders.edit');

        BulkAction::create(PrintShippingLabelsBulkAction::class, OrdersResourceList::class)
            ->title('Print Shipping Labels')
            ->permissions('orders.edit');

        BulkAction::create(CancelFulfillmentsBulkAction::class, FulfillmentsResourceList::class)
            ->title('Cancel Fulfillments')
            ->permissions('fulfillments.view');

        BulkAction::create(DeleteFulfillmentsBulkAction::class, FulfillmentsResourceList::class)
            ->title('Delete Canceled Fulfillments')
            ->permissions('fulfillments.view');

        BulkAction::create(DownloadLabelsBulkAction::class, FulfillmentsResourceList::class)
            ->title('Print Labels')
            ->permissions('fulfillments.view');
    }

    public function assetLinks()
    {
        return [
            'techquity/couriers' => __DIR__ . '/../public',
        ];
    }
}
