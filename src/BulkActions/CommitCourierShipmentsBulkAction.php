<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Techquity\Aero\Couriers\Abstracts\AbstractQueueableBulkAction;
use Techquity\Aero\Couriers\Actions\CommitShipments;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;

class CommitCourierShipmentsBulkAction extends AbstractQueueableBulkAction
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
