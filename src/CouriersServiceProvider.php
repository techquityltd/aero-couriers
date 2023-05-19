<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\{AdminModule, AdminSlot, BulkAction, Permissions};
use Aero\Admin\BulkActions\DeleteShippingMethodsBulkAction;
use Aero\Admin\Http\Requests\Settings\{CreateFulfillmentMethodRequest, UpdateFulfillmentMethodRequest};
use Aero\Admin\Http\Requests\Orders\{CreateFulfillmentRequest, UpdateFulfillmentRequest};
use Aero\Admin\Http\Responses\Configuration\{AdminFulfillmentMethodCreatePage, AdminFulfillmentMethodEditPage, AdminFulfillmentMethodStore, AdminFulfillmentMethodUpdate};
use Aero\Admin\Http\Responses\Orders\{AdminOrderFulfillmentCreatePage, AdminOrderFulfillmentEditPage, AdminOrderFulfillmentStore, AdminOrderFulfillmentUpdate};
use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Common\Providers\ModuleServiceProvider;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Aero\Responses\ResponseBuilder;
use Techquity\Aero\Couriers\BulkActions\CollectShipmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\CommitCourierShipmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\CompletePendingFulfillments;
use Techquity\Aero\Couriers\BulkActions\DeleteCourierConnectorsBulkAction;
use Techquity\Aero\Couriers\BulkActions\DeleteCourierServicesBulkAction;
use Techquity\Aero\Couriers\Models\{CourierConnector, CourierService, CourierShipment, PendingLabel};
use Techquity\Aero\Couriers\BulkActions\PrintLabelsBulkAction;
use Techquity\Aero\Couriers\ResourceLists\CourierConnectorsResourceList;
use Techquity\Aero\Couriers\ResourceLists\CourierServicesResourceList;
use Techquity\Aero\Couriers\BulkActions\MergeCourierShipmentsBulkAction;
use Techquity\Aero\Couriers\BulkActions\ShipOrdersBulkAction;
use Techquity\Aero\Couriers\Http\Responses\Steps\{SaveFulfillmentMethodCourierOptions, CreateAndAttachShipment, UpdateFulfillmentShipment};
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CouriersServiceProvider extends ModuleServiceProvider
{
    use UsesCourierDriver;

    /**
     * Bootstrap the application services.
     */
    public function setup()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'couriers');

        // Add the permissions used for managing couriers...
        Permissions::add([
            'couriers.manage-shipments',
            'couriers.manage-services',
            'couriers.manage-connectors',
            'couriers.manage-collections',
        ]);

        // Macro an attribute to determine if the method is courier...
        FulfillmentMethod::macro('getIsCourierAttribute', function () {
            return $this->getDriver() instanceof CourierDriver;
        });

        // Add Courier Service relation...
        FulfillmentMethod::macro('courierService', function () {
            return $this->belongsTo(CourierService::class, 'courier_service_id');
        });

        // Add the Courier Connector relation...
        FulfillmentMethod::macro('courierConnector', function () {
            return $this->belongsTo(CourierConnector::class, 'courier_connector_id');
        });

        Fulfillment::makeFillable('courier_shipment_id');
        Fulfillment::macro('courierShipment', function () {
            return $this->belongsTo(CourierShipment::class, 'courier_shipment_id', 'id');
        });

        FulfillmentsResourceList::extend(function (FulfillmentsResourceList $list) {
            $list->addColumnAfter('Courier', function ($row) {
                if ($row->courierShipment) {
                    return optional($row->courierShipment->courierService)->carrier;
                }
            }, 'orders');
        });

        AdminSlot::inject('orders.index.header.buttons', function ($_) {
            return view('couriers::slots.pending-labels');
        });

        $this->configureCourierManagerModule();

        $this->configureFulfillmentMethodsSetup();

        $this->configureFulfillmentSetup();

        $this->configureFulfillmentBulkActions();
    }

    /**
     * Configure and extend the courier manager module.
     */
    protected function configureCourierManagerModule(): void
    {
        // Create a new module for managing couriers...
        AdminModule::create('shipment-manager')
            ->title('Courier Manager')
            ->summary('Manage your couriers and shipments.')
            ->permissions('couriers.manage-shipments')
            ->routes(__DIR__ . '/../routes/admin.php')
            ->route('admin.courier-manager.shipments.index');

        // Add the links to manage other aspects of the module...
        AdminSlot::inject('couriers.shipments.index.header.buttons', function ($_) {
            return view('couriers::resource-lists.manage');
        });

        // Add the refresh services link to the resource list services...
        AdminSlot::inject('couriers.services.index.header.buttons', function ($_) {
            return view('couriers::resource-lists.refresh-services');
        });

        BulkAction::create(MergeCourierShipmentsBulkAction::class, CourierShipmentsResourceList::class)
            ->title('Merge Shipments')
            ->permissions('couriers.manage-shipments');

        BulkAction::create(CommitCourierShipmentsBulkAction::class, CourierShipmentsResourceList::class)
            ->title('Commit Shipments')
            ->permissions('couriers.manage-shipments');

        BulkAction::create(DeleteShippingMethodsBulkAction::class, CourierShipmentsResourceList::class)
            ->title('Delete Shipments')
            ->permissions('couriers.manage-shipments')
            ->confirm()
            ->confirmTitle('Are you sure?')
            ->confirmText('This will delete the selected shipments and fulfillments!');

        BulkAction::create(CollectShipmentsBulkAction::class, CourierShipmentsResourceList::class)
            ->title('Collect Shipments')
            ->permissions('couriers.manage-shipments')
            ->confirm()
            ->confirmText('This will mark the selected shipments as collected and orders as dispatched!');

        BulkAction::create(DeleteCourierConnectorsBulkAction::class, CourierConnectorsResourceList::class)
            ->title('Delete Connectors')
            ->permissions('couriers.manage-connectors');

        BulkAction::create(DeleteCourierServicesBulkAction::class, CourierServicesResourceList::class)
            ->title('Delete Services')
            ->permissions('couriers.manage-services');

        BulkAction::create(ShipOrdersBulkAction::class, OrdersResourceList::class)
            ->title('Ship Orders')
            ->permissions('couriers.manage-shipments');
    }

    /**
     * Configure the fulfillment method courier configuration.
     */
    protected function configureFulfillmentMethodsSetup(): void
    {
        /**
         * CREATE
         */
        AdminFulfillmentMethodCreatePage::extend(function (ResponseBuilder $builder) {
            $this->attachCourierOptionsData($builder);
            $this->attachCourierDrivers($builder);

            AdminSlot::inject('configuration.fulfillment-methods.new.cards', static::$selector_view);
        });

        $this->extendRequestForSelector(CreateFulfillmentMethodRequest::class);

        AdminFulfillmentMethodStore::extend(SaveFulfillmentMethodCourierOptions::class);

        /**
         * UPDATE
         */
        AdminFulfillmentMethodEditPage::extend(function (ResponseBuilder $builder) {
            $this->attachCourierOptionsData($builder);
            $this->attachCourierDrivers($builder);

            AdminSlot::inject('configuration.fulfillment-methods.edit.cards', static::$selector_view);
        });

        $this->extendRequestForSelector(UpdateFulfillmentMethodRequest::class);

        AdminFulfillmentMethodUpdate::extend(SaveFulfillmentMethodCourierOptions::class);
    }

    protected function configureFulfillmentSetup(): void
    {
        /**
         * CREATE
         */
        AdminOrderFulfillmentCreatePage::extend(function (ResponseBuilder $builder) {
            $this->attachCourierOptionsData($builder);

            // Allows us to completetly override the method...
            if (request()->query('override-method')) {
                $builder->setData('methods', FulfillmentMethod::ordered()->get());
            }

            $this->attachCourierMethods($builder);

            AdminSlot::inject('orders.fulfillment.new.cards', static::$selector_view);
        });

        $this->extendRequestForSelector(CreateFulfillmentRequest::class);

        AdminOrderFulfillmentStore::extend(CreateAndAttachShipment::class);

        /**
         * UPDATE
         */
        AdminOrderFulfillmentEditPage::extend(function (ResponseBuilder $builder) {
            if (!$shipment = $builder->fulfillment->courierShipment) {
                return;
            }

            $this->attachCourierOptionsData($builder);
            $this->attachCourierMethods($builder);

            $builder->setData('shipment', $shipment);

            AdminSlot::inject('orders.fulfillment.edit.cards', static::$selector_view);
            AdminSlot::inject('orders.fulfillment.edit.cards.extra.top', 'couriers::slots.fulfillments.information');
        });

        $this->extendRequestForSelector(UpdateFulfillmentRequest::class);

        AdminOrderFulfillmentUpdate::extend(UpdateFulfillmentShipment::class);
    }

    protected function configureFulfillmentBulkActions(): void
    {
        BulkAction::create(CompletePendingFulfillments::class, FulfillmentsResourceList::class)
            ->title('Complete pending fulfillments')
            ->permissions('fulfillments.view');

        BulkAction::create(PrintLabelsBulkAction::class, FulfillmentsResourceList::class)
            ->title('Print labels')
            ->permissions('fulfillments.view');
    }
}
