<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Techquity\Aero\Couriers\Actions\CommitShipments;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;

class CommitCourierShipmentsBulkAction extends BulkActionJob
{
    protected $list;

    public function __construct(CourierShipmentsResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        (new CommitShipments())($this->list->items());
    }
}
