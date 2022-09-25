<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\Order;
use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Abstracts\AbstractQueueableBulkAction;
use Techquity\Aero\Couriers\Actions\CommitShipments;
use Techquity\Aero\Couriers\Actions\CreateFulfillment;
use Techquity\Aero\Couriers\Actions\CreateShipment;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class ShipOrdersBulkAction extends AbstractQueueableBulkAction
{
    use UsesCourierDriver;

    protected $list;

    public function __construct(OrdersResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $shipments = $this->list->items()
            ->reject(fn (Order $order) => (bool) $order->isFullyAllocated())
            ->filter(fn (Order $order) => (bool) $order->shippingMethod)
            ->filter(
                fn (Order $order) => $order
                    ->shippingMethod
                    ->fulfillmentMethods
                    ->filter(fn ($method) => $method->isCourier)
                    ->count()
            )
            ->map(fn (Order $order) => (new CreateFulfillment($order))->fulfillment)
            ->map(fn (Fulfillment $fulfillment) => (new CreateShipment($fulfillment))->shipment)
            ->all();

        if (count($shipments)) {
            (new CommitShipments())(collect($shipments));
        }
    }
}
