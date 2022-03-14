<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteFulfillmentsBulkAction extends BulkActionJob implements ShouldQueue
{
    protected FulfillmentsResourceList $list;

    protected string $admin;

    public function __construct(FulfillmentsResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $this->list->items()
            ->filter(fn (Fulfillment $fulfillment) => $fulfillment->isCanceled())
            ->each(fn (Fulfillment $fulfillment) => $fulfillment->delete());
    }

    public function response()
    {
        return back()->with([
            'message' => 'Fulfillments are now being deleted',
        ]);
    }
}
