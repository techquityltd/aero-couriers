<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\Order;
use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Actions\CommitShipments;
use Techquity\Aero\Couriers\Actions\CreateFulfillment;
use Techquity\Aero\Couriers\Actions\CreateShipment;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class ShipOrdersBulkAction extends BulkActionJob
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
            ->map(fn (Order $order) => (new CreateFulfillment($order))->fulfillment)
            ->map(fn (Fulfillment $fulfillment) => (new CreateShipment($fulfillment))->shipment)
            ->all();

        if (count($shipments)) {
           (new CommitShipments())(collect($shipments));
        }
    }
}
