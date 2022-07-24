<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\AdminSlot;
use Aero\Admin\BulkAction;
use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Common\Facades\Settings;
use Aero\Common\Providers\ModuleServiceProvider;
use Aero\Common\Settings\SettingGroup;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Techquity\Aero\Couriers\BulkActions\CancelFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\CreateFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\DeleteFulfillmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\DispatchOrdersBulkAction;
use Techquity\Aero\Couriers\BulkActions\DownloadLabelsBulkAction;
use Techquity\Aero\Couriers\BulkActions\PrintShippingLabelsBulkAction;
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

        FulfillmentMethodInstallation::setup();
        FulfillmentInstallation::setup();
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
