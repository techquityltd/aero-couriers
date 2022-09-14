<?php

namespace Techquity\Aero\Couriers\Actions;

use Aero\Cart\Models\Order;
use Aero\Cart\Models\OrderItem;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentAddress;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Illuminate\Support\Collection;
use Techquity\Aero\Couriers\CourierDriver;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CreateFulfillment
{
    use UsesCourierDriver;

    /**
     * The order that owns the fulfillment.
     */
    protected Order $order;

    /**
     * The new fulfillment instance.
     */
    protected Fulfillment $fulfillment;

    /**
     * The fulfillment method used for the fulfillment.
     */
    protected FulfillmentMethod $fulfillmentMethod;

    /**
     * The fulfillment address created from the shipping address.
     */
    protected FulfillmentAddress $fulfillmentAddress;

    /**
     * The courier driver used for this fulfillment.
     */
    protected CourierDriver $fulfillmentDriver;

    /**
     * The items that can be allocated.
     */
    protected Collection $unallocatedItems;

    /**
     * Create a new Create Fulfillment instance.
     */
    public function __construct(Order $order)
    {
        $this->fulfillment = new Fulfillment([
            'state' => (new CourierDriver())->getDefaultState(),
        ]);

        $this->fulfillment->mobile = $order->shippingAddress->mobile ?? $order->shippingAddress->phone;
        $this->fulfillment->email = $order->email;

        $this->order = $order;

        $this->fulfillmentAddress = FulfillmentAddress::create(
            $this->order->shippingAddress->toFulfillmentAddress()
        );
    }

    /**
     * Set the fulfillment method that the fulfillment will use.
     */
    public function usingFulfillmentMethod(FulfillmentMethod $fulfillmentMethod): self
    {
        $this->fulfillmentMethod = $fulfillmentMethod;

        $this->fulfillmentDriver = $this->fulfillmentMethod->getDriver();

        return $this;
    }

    /**
     * Set the items that will be allocated to this fulfillment.
     */
    public function setUnallocatedItems(Collection $items): self
    {
        $this->unallocatedItems = $items;

        return $this;
    }

    /**
     * Get all unallocated items from the order.
     */
    protected function getAllUnallocatedItems(): Collection
    {
        $this->setUnallocatedItems(
            $this->order->items->filter
                ->canBeAllocated()
                ->mapWithKeys(function (OrderItem $item) {
                    return [$item->id => ['quantity' => $item->unallocated_quantity]];
                })
        );

        return $this->unallocatedItems;
    }

    /**
     * Set the fulfillment method that will be used.
     */
    protected function setFulfillmentMethod(): void
    {
        $this->usingFulfillmentMethod(
            $this->order->shippingMethod->fulfillmentMethods()
                ->whereIn('driver', $this->getCourierDrivers()->keys()->toArray())
                ->first()
        );
    }

    /**
     * Dynamically access the classes attributes.
     */
    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }

    /**
     * Finish off the fulfillment upon the object's destruction.
     */
    public function __destruct()
    {
        if (!isset($this->fulfillmentMethod)) {
            $this->setFulfillmentMethod();
        }

        $this->fulfillment->save();

        $this->fulfillment->method()->associate($this->fulfillmentMethod);
        $this->fulfillment->address()->associate($this->fulfillmentAddress);

        $this->fulfillmentDriver->setFulfillments(collect()->push($this->fulfillment));

        $this->fulfillment->items()->sync(
            (isset($this->unallocatedItems) ? $this->unallocatedItems : $this->getAllUnallocatedItems())->toArray()
        );
    }
}
