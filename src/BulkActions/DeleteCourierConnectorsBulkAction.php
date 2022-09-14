<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Techquity\Aero\Couriers\Models\CourierConnector;
use Techquity\Aero\Couriers\ResourceLists\CourierConnectorsResourceList;

class DeleteCourierConnectorsBulkAction extends BulkActionJob
{
    protected $list;

    public function __construct(CourierConnectorsResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $this->list->items()->each(function (CourierConnector $connector) {
            $connector->delete();
        });
    }
}
