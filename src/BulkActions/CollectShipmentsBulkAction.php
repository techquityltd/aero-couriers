<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Techquity\Aero\Couriers\Actions\CollectShipments;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CollectShipmentsBulkAction extends BulkActionJob
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
