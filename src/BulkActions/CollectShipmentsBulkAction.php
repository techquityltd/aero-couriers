<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Techquity\Aero\Couriers\Abstracts\AbstractQueueableBulkAction;
use Techquity\Aero\Couriers\Actions\CollectShipments;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CollectShipmentsBulkAction extends AbstractQueueableBulkAction
{
    use UsesCourierDriver;

    protected $list;

    public function __construct(CourierShipmentsResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $shipments = $this->list->items()
            ->filter(fn (CourierShipment $shipment) => (bool) $shipment->committed)
            ->all();

        if (count($shipments)) {
           (new CollectShipments())(collect($shipments));
        }
    }
}
