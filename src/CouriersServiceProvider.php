<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\AdminSlot;
use Aero\Admin\BulkAction;
use Aero\Admin\Http\Responses\Configuration\AdminFulfillmentMethodStore;
use Aero\Admin\Http\Responses\Configuration\AdminFulfillmentMethodUpdate;
use Aero\Admin\Http\Responses\Configuration\AdminShippingMethodStore;
use Aero\Admin\Http\Responses\Configuration\AdminShippingMethodUpdate;
use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\ShippingMethod;
use Aero\Common\Facades\Settings;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Techquity\Aero\Couriers\BulkActions\CancelFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\CreateFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\DeleteFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\DownloadLabelsBulkAction;
use Techquity\Aero\Couriers\Facades\Courier;
use Techquity\Aero\Couriers\Http\Middleware\ValidateFulfillmentMethodCourierConfiguration;
use Techquity\Aero\Couriers\Http\Middleware\ValidateShippingMethodCourierConfiguration;
use Techquity\Aero\Couriers\Http\Responses\Steps\SaveFulfillmentMethodCourierConfiguration;
use Techquity\Aero\Couriers\Http\Responses\Steps\SaveShippingMethodCourierConfiguration;
use Techquity\Aero\Couriers\Models\FulfillmentLog;

class CouriersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('courier', function ($app) {
            return new CourierManager($app);
        });
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/admin/views', 'courier');

        Settings::group('courier', function (SettingGroup $group) {
            $group->boolean('automatic_process')
                ->label('Process fulfillments automatically after creating fulfillments.')
                ->default(false);
        });

        Relation::morphMap([
            CourierDriver::NAME => CourierDriver::class,
        ]);

        $this->extendFulfillmentMethodConfiguration();
        $this->extendShippingMethodConfiguration();
        $this->addLoggingToFulfillments();

        BulkAction::create(CreateFulfillmentsBulkAction::class, OrdersResourceList::class)
            ->title('Create Fulfillments')
            ->permissions('orders.edit');

        BulkAction::create(CancelFulfillmentsBulkAction::class, FulfillmentsResourceList::class)
            ->title('Cancel Fulfillments')
            ->permissions('fulfillments.view');

        BulkAction::create(DeleteFulfillmentsBulkAction::class, FulfillmentsResourceList::class)
            ->title('Delete Canceled Fulfillments')
            ->permissions('fulfillments.view');

        BulkAction::create(DownloadLabelsBulkAction::class, FulfillmentsResourceList::class)
            ->title('Download Labels')
            ->permissions('fulfillments.view');
    }

    protected function extendFulfillmentMethodConfiguration()
    {
        $fulfillmentConfiguration = (function ($data) {
            $data['couriers'] = collect(Courier::getFulfillmentMethodConfiguration())
                ->mapWithKeys(fn ($driver, $key) => [$key => $driver->types()])
                ->toArray();

            return view('courier::fulfillment-methods.configuration', $data);
        });

        AdminSlot::inject('configuration.fulfillment-methods.new.cards', $fulfillmentConfiguration);
        AdminSlot::inject('configuration.fulfillment-methods.edit.cards', $fulfillmentConfiguration);

        FulfillmentMethod::makeFillable(['courier', 'courierConfig']);

        FulfillmentMethod::macro('courierConfig', function ($key = null, $courier = null) {

            $decoded = data_get(json_decode($this->courier_configuration, true), $courier ?? $this->courier, []);

            return $key ? data_get($decoded, $key) : $decoded;
        });

        AdminFulfillmentMethodUpdate::middleware(ValidateFulfillmentMethodCourierConfiguration::class);
        AdminFulfillmentMethodStore::middleware(ValidateFulfillmentMethodCourierConfiguration::class);

        AdminFulfillmentMethodUpdate::extend(SaveFulfillmentMethodCourierConfiguration::class);
        AdminFulfillmentMethodStore::extend(SaveFulfillmentMethodCourierConfiguration::class);
    }

    protected function extendShippingMethodConfiguration()
    {
        $shippingMethodConfiguration = (function ($data) {
            $data['couriers'] = collect(Courier::getShippingMethodConfiguration())
                ->mapWithKeys(fn ($driver, $key) => [$key => $driver->types()])
                ->toArray();

            return view('courier::shipping-methods.configuration', $data);
        });

        AdminSlot::inject('configuration.shipping-methods.new.cards', $shippingMethodConfiguration);
        AdminSlot::inject('configuration.shipping-methods.edit.cards', $shippingMethodConfiguration);

        ShippingMethod::makeFillable(['courier', 'courierConfig']);

        ShippingMethod::macro('courierConfig', function ($key = null, $courier = null) {
            $decoded = data_get(json_decode($this->courier_configuration, true), $courier ?? $this->courier, []);

            return $key ? data_get($decoded, $key) : $decoded;
        });

        AdminShippingMethodUpdate::middleware(ValidateShippingMethodCourierConfiguration::class);
        AdminShippingMethodStore::middleware(ValidateShippingMethodCourierConfiguration::class);

        AdminShippingMethodUpdate::extend(SaveShippingMethodCourierConfiguration::class);
        AdminShippingMethodStore::extend(SaveShippingMethodCourierConfiguration::class);
    }

    protected function addLoggingToFulfillments()
    {
        Fulfillment::macro('logs', function () {
            return $this->hasMany(FulfillmentLog::class)->orderByDesc('id');
        });

        AdminSlot::inject('orders.fulfillment.edit.cards', 'courier::fulfillments.logs');
    }
}
