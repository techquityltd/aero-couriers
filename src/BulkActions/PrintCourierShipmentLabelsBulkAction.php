<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Illuminate\Support\Facades\Auth;
use Aero\Admin\Jobs\BulkActionJob;
use Techquity\Aero\Couriers\Actions\PrintLabels;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class PrintCourierShipmentLabelsBulkAction extends BulkActionJob
{
    use UsesCourierDriver;

    protected $shipments;
    protected $admin;

    public function __construct(CourierShipmentsResourceList $list)
    {
        $this->shipments = $list->items();
        $this->admin = Auth::user();
    }

    public function handle(): void
    {
        if ($this->shipments->count()) {
            (new PrintLabels())(collect($this->shipments), $this->admin);
        }
    }
}
