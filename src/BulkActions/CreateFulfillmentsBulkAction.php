<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\Jobs\BulkActionJob;
use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\Order;
use Aero\Cart\Models\OrderItem;
use Aero\Cart\Models\OrderStatus;
use Aero\Fulfillment\FulfillmentDriver;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Support\Collection;
use Techquity\Aero\Couriers\Jobs\ProcessPendingFulfillment;
use Techquity\Aero\Couriers\Models\FulfillmentLog;

class CreateFulfillmentsBulkAction extends BulkActionJob
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
                // Unallocated items.
                $items = $order->items->filter
                    ->canBeAllocated()
                    ->mapWithKeys(function (OrderItem $item) {
                        return [$item->id => ['quantity' => $item->unallocated_quantity]];
                    });

                // Get the shipping driver
                $driver = $order->shippingMethod->fulfillmentMethods()->first()->getDriver();

                if ($items->isNotEmpty($order) && $driver) {
                    $this->createFulfillment($order, $items, $driver);
                }
            })
            ->all();
    }

    protected function createFulfillment(Order $order, Collection $items, FulfillmentDriver $fulfillmentDriver): void
    {
        $fulfillment = new Fulfillment([
            'state' => $fulfillmentDriver->getDefaultState(),
            'mobile' => $order->shippingAddress->mobile ?? $order->shippingAddress->phone,
            'email' => $order->email,
        ]);

        $fulfillment->method()->associate($order->shippingMethod->fulfillmentMethods()->first());
        $fulfillment->address()->associate(
            $fulfillment->address()->create($order->shippingAddress->toFulfillmentAddress())
        );
        $fulfillment->save();

        $fulfillment->items()->sync($items->toArray());

        $fulfillment->logs()->create([
            'type' => FulfillmentLog::INFO,
            'title' => 'Fulfillment Created',
            'message' => "Fulfillment created by {$this->admin}",
        ]);

        $order->setOrderStatus(
            OrderStatus::firstWhere('state', OrderStatus::PROCESSING)
        );

        if (setting('courier.automatic_process')) {
            ProcessPendingFulfillment::dispatch($fulfillment)->onQueue('courier');
        };
    }

    public function response()
    {
        return back()->with([
            'message' => 'Fulfillments created for eligible orders.',
        ]);
    }
}
