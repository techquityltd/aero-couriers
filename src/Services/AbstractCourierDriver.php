<?php

namespace Techquity\Aero\Couriers\Services;

use Aero\Cart\Models\Order;
use Aero\Cart\Models\ShippingMethod;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;

abstract class AbstractCourierDriver
{
    /**
     * The attached fulfillment.
     */
    protected $fulfillment;

    /**
     * Merged options from the shipping and fulfillment methods.
     */
    protected array $configuration;

    /**
     * Attach a fulfillment to the method
     */
    public function attachFulfillment(Fulfillment $fulfillment): self
    {
        $this->fulfillment = $fulfillment->load(['method', 'items.order.shippingMethod']);

        $this->setConfiguration();

        return $this;
    }

    public function setConfiguration(array $config = []): self
    {
        if (!empty($config)) {
            $this->configuration = $config;
        } else {
            $this->configuration = array_merge(
                $this->getFulfillmentMethod()->courierConfig(),
                $this->getShippingMethod()->courierConfig(null, $this->getFulfillmentMethod()->courier)
            );
        }

        return $this;
    }

    /**
     * Get a fulfillment method from the fulfillment
     */
    public function getFulfillmentMethod(): FulfillmentMethod
    {
        return $this->fulfillment->method;
    }

    /**
     * Get a shipping method from the fulfillment
     */
    public function getShippingMethod(): ShippingMethod
    {
        return $this->fulfillment->items->first()->order->shippingMethod;
    }

    /**
     * Get the order for the fulfillment.
     */
    public function order(): Order
    {
        return $this->fulfillment->items()->first()->order;
    }
}
