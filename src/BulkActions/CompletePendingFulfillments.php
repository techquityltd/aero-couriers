<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\ResourceLists\FulfillmentsResourceList;
use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Abstracts\AbstractQueueableBulkAction;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CompletePendingFulfillments extends AbstractQueueableBulkAction
{
    use UsesCourierDriver;

    protected $list;

    public function __construct(FulfillmentsResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $this->list->items()->each(function (Fulfillment $fulfillment) {
            if ($fulfillment->state === Fulfillment::PENDING) {
                $fulfillment->state = Fulfillment::SUCCESSFUL;
                $fulfillment->save();
            }
        });
    }
}
