<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Support\Facades\Auth;
use Techquity\Aero\Couriers\Abstracts\AbstractQueueableBulkAction;
use Techquity\Aero\Couriers\Actions\PrintLabels;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class PrintLabelsBulkAction extends AbstractQueueableBulkAction
{
    use UsesCourierDriver;

    protected $list;
    protected $admin;

    public function __construct(FulfillmentsResourceList $list)
    {
        $this->list = $list;
        $this->admin = Auth::user();
    }

    public function handle(): void
    {
        if (! $this->admin) {
            return;
        }

        $shipments = $this->list->items()
            ->filter(fn (Fulfillment $fulfillment) => $fulfillment->courierShipment)
            ->filter(fn (Fulfillment $fulfillment) => $fulfillment->courierShipment->consignment_number)
            ->map(fn (Fulfillment $fulfillment) => $fulfillment->courierShipment)
            ->all();

        if (count($shipments)) {
            (new PrintLabels())(collect($shipments), $this->admin);
        }
    }
}
