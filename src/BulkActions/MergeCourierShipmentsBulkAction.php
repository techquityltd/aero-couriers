<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;

class MergeCourierShipmentsBulkAction extends BulkActionJob
{
    protected $list;

    public function __construct(CourierShipmentsResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        // Cannot merge committed shipments.
        if ($this->list->items()->where('committed', true)->count()) {
            return;
        }

        // Key shipment configurations need to be the same in order to merge.
        if ($this->list->items()->unique(function (CourierShipment $shipment) {
            return "{$shipment->courier_connector_id}.{$shipment->courier_service_id}";
        })->count() === $this->list->items()->count()) {
            return;
        }

        // The shipping addresses should match in order to merge
        if ($this->list->items()->map(function (CourierShipment $shipment) {
            return  $shipment->fulfillments->map(function (Fulfillment $fulfillment) {
                return "{$fulfillment->address->line_1} {$fulfillment->address->postcode}";
            });
        })->flatten()->unique()->count() !== 1) {
            return;
        }

        $parent = $this->list->items()->first();

        $this->list->items()->each(function (CourierShipment $shipment) use ($parent) {
            if ($parent->is($shipment)) {
                return;
            }

            // Assign fulfillments to parent.
            $shipment->fulfillments->each(function (Fulfillment $fulfillment) use ($parent) {
                $fulfillment->courier_shipment_id = $parent->id;
                $fulfillment->save();
            });

            $shipment->delete();
        });
    }
}
