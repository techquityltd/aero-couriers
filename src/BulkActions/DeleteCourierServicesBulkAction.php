<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Techquity\Aero\Couriers\Abstracts\AbstractQueueableBulkAction;
use Techquity\Aero\Couriers\Models\CourierService;
use Techquity\Aero\Couriers\ResourceLists\CourierServicesResourceList;

class DeleteCourierServicesBulkAction extends AbstractQueueableBulkAction
{
    protected $list;

    public function __construct(CourierServicesResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $this->list->items()->each(function (CourierService $connector) {
            if ($connector->trashed()) {
                $connector->restore();
            } else {
                $connector->delete();
            }
        });
    }
}
