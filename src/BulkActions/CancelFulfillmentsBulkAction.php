<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Techquity\Aero\Couriers\Facades\Courier;
use Techquity\Aero\Couriers\Models\FulfillmentLog;

class CancelFulfillmentsBulkAction extends BulkActionJob implements ShouldQueue
{
    protected FulfillmentsResourceList $list;

    protected string $admin;

    public function __construct(FulfillmentsResourceList $list)
    {
        $this->list = $list;
        $this->admin = auth()->user()->name;
    }

    public function handle(): void
    {
        $this->list->items()->each(function (Fulfillment $fulfillment) {
            $driver = Courier::forFulfillment($fulfillment);
            $driver->cancelConsignment();
        });
    }

    /**
     * The successful response to return.
     */
    public function response()
    {
        return back()->with([
            'message' => 'Fulfillments are now being cancelled',
        ]);
    }
}
