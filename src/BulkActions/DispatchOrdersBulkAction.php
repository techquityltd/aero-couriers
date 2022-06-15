<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\Order;
use Aero\Fulfillment\Models\Fulfillment;

class DispatchOrdersBulkAction extends BulkActionJob
{
    protected OrdersResourceList $list;

    protected string $admin;

    public function __construct(OrdersResourceList $list)
    {
        $this->admin = auth()->user()->name;
        $this->list = $list;
    }

    public function handle(): void
    {
        $this->list->items()
            ->filter(fn (Order $order) => (bool) $order->shippingMethod)
            ->filter(fn (Order $order) => $order->shippingMethod->fulfillmentMethods()->count() === 1)
            ->filter(fn (Order $order) => (bool) $order->shippingAddress)
            ->each(function (Order $order) {
                // must be already processing
                $order->fulfillments->each(function (Fulfillment $fulfillment) {
                    // fulfillment must be open
                    if ($fulfillment->isOpen()) {
                        $fulfillment->method->getDriver()->setFulfillments(collect([$fulfillment]))->handle();
                    }
                });
            })
            ->all();
    }

    public function response()
    {
        return back()->with([
            'message' => 'Fulfillments created for eligible orders.',
        ]);
    }
}
