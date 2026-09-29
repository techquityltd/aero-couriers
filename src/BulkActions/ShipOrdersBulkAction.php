<?php

namespace Techquity\Aero\Couriers\BulkActions;

use Aero\Admin\ResourceLists\OrdersResourceList;
use Aero\Cart\Models\Order;
use Aero\Cart\Models\OrderItem;
use Aero\Cart\Models\OrderItemGroup;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;
use Techquity\Aero\Couriers\Abstracts\AbstractQueueableBulkAction;
use Techquity\Aero\Couriers\Actions\CommitShipments;
use Techquity\Aero\Couriers\Actions\CreateFulfillment;
use Techquity\Aero\Couriers\Actions\CreateShipment;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class ShipOrdersBulkAction extends AbstractQueueableBulkAction
{
    use UsesCourierDriver;

    protected $list;

    public function __construct(OrdersResourceList $list)
    {
        $this->list = $list;
    }

    public function handle(): void
    {
        $group = new OrderItemGroup();

        $groupAddressesAvailable = method_exists($group, 'relationResolver')
            ? (bool) $group->relationResolver(OrderItemGroup::class, 'shippingAddress')
            : Schema::hasColumn('order_item_groups', 'order_address_id');

        $shipments = $this->list->items()->reduce(function (Collection $shipments, Order $order) use ($groupAddressesAvailable) {
            $this->fulfillmentAssignments($order, $groupAddressesAvailable)->each(function (array $assignment) use ($order, $shipments) {
                if (! $assignment['shipping_method'] || ! $assignment['shipping_address']) {
                    return;
                }

                $fulfillmentMethod = $assignment['shipping_method']->fulfillmentMethods->first(function (FulfillmentMethod $method) {
                    return $method->isCourier;
                });

                if (! $fulfillmentMethod) {
                    return;
                }

                $fulfillment = (new CreateFulfillment($order, $assignment['shipping_address']))
                    ->usingFulfillmentMethod($fulfillmentMethod)
                    ->setUnallocatedItems($assignment['items'])
                    ->fulfillment;

                $shipments->push((new CreateShipment($fulfillment))->shipment);
            });

            return $shipments;
        }, collect());

        if ($shipments->isEmpty()) {
            return;
        }

        (new CommitShipments())($shipments, Auth::guard(config('aero.admin.auth.defaults.guard'))->user());
    }

    protected function fulfillmentAssignments(Order $order, bool $groupAddressesAvailable): Collection
    {
        $relations = [
            'shippingAddress',
            'items.group.shippingMethod',
        ];

        if ($groupAddressesAvailable) {
            $relations[] = 'items.group.shippingAddress';
        }

        $order->loadMissing($relations);

        $items = $order->items->filter(function (OrderItem $item) {
            return $item->canBeAllocated() && $this->itemIsAvailable($item);
        });

        if ($items->isEmpty()) {
            return collect();
        }

        if (! $items->contains(function (OrderItem $item) {
            return (bool) $item->order_item_group_id;
        })) {
            return collect([
                $this->fulfillmentAssignment($order->shippingMethod, $order->shippingAddress, $items),
            ]);
        }

        return $items->reduce(function (Collection $assignments, OrderItem $item) use ($order, $groupAddressesAvailable) {
            $shippingMethod = optional($item->group)->shippingMethod ?? $order->shippingMethod;

            $shippingAddress = $groupAddressesAvailable
                ? optional($item->group)->shippingAddress ?? $order->shippingAddress
                : $order->shippingAddress;

            $key = optional($shippingMethod)->getKey().'-'.optional($shippingAddress)->getKey();

            $assignment = $assignments->get($key, $this->fulfillmentAssignment($shippingMethod, $shippingAddress));

            $assignment['items']->put($item->id, [
                'quantity' => $item->unallocated_quantity,
            ]);

            return $assignments->put($key, $assignment);
        }, collect());
    }

    protected function fulfillmentAssignment($shippingMethod, $shippingAddress, ?Collection $items = null): array
    {
        return [
            'shipping_method' => $shippingMethod,
            'shipping_address' => $shippingAddress,
            'items' => ($items ?? collect())->mapWithKeys(function (OrderItem $item) {
                return [
                    $item->id => [
                        'quantity' => $item->unallocated_quantity,
                    ],
                ];
            }),
        ];
    }

    protected function itemIsAvailable(OrderItem $item): bool
    {
        $group = optional($item->group)->key ?? '';

        if (Str::startsWith($group, ['preorder:', 'backorder:'])) {
            return $this->dateIsAvailable(Str::after($group, ':'), 'Y-m-d');
        }

        $option = collect($item->options)->first(function (array $option): bool {
            return in_array(data_get($option, 'name'), [
                trans('pre-order-products::pre-order.cart-option'),
                trans('pre-order-products::back-order.cart-option'),
            ], true);
        });

        return ! $option
            || $this->dateIsAvailable(data_get($option, 'value'), trans('pre-order-products::pre-order.date-format'));
    }

    protected function dateIsAvailable(?string $date, string $format): bool
    {
        if (! $date || $date === 'undated') {
            return false;
        }

        try {
            return Carbon::createFromFormat($format, $date)->startOfDay()->lte(today());
        } catch (Throwable $exception) {
            return false;
        }
    }
}
