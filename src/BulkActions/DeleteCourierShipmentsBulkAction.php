<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;

class DeleteCourierShipmentBulkAction extends BulkActionJob
{
    protected $list;

    public function __construct(CourierShipmentsResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $this->list->items()->each(function (CourierShipment $shipment) {
            $shipment->delete();

            $shipment->fulfillments()->each(function ($fulfillment) {
                $fulfillment->delete();
            });
        });
    }
}
