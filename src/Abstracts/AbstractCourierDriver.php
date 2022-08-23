<?php

namespace Techquity\Aero\Couriers\Abstracts;

use Aero\Cart\Models\Order;
use Aero\Cart\Models\ShippingMethod;
use Aero\Fulfillment\Contracts\Response;
use Aero\Fulfillment\FulfillmentDriver;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Aero\Fulfillment\Responses\FulfillmentResponse;

abstract class AbstractCourierDriver extends FulfillmentDriver
{
    public const METHOD_ONLY_SECTION = 'method';

    /**
     * The default fulfillment state of a fulfillment when created.
     *
     * @return string
     */
    public function getDefaultState(): string
    {
        return Fulfillment::OPEN;
    }

    /**
     * Make the fulfillment request.
     *
     * @return \Aero\Fulfillment\Contracts\Response
     */
    public function handle(): Response
    {
        $response = new FulfillmentResponse();

        $response->setSuccessful(true);

        return $response;
    }

    public static function getFulfillmentSettingsKey($prefix = '')
    {
        return $prefix . static::KEY;
    }

    // /**
    //  * The attached fulfillment.
    //  */
    // protected $fulfillment;

    // /**
    //  * Merged options from the shipping and fulfillment methods.
    //  */
    // protected array $configuration;

    // /**
    //  * Attach a fulfillment to the method
    //  */
    // public function attachFulfillment(Fulfillment $fulfillment): self
    // {
    //     $this->fulfillment = $fulfillment->load(['method', 'items.order.shippingMethod']);

    //     $this->setConfiguration();

    //     return $this;
    // }

    // public function setConfiguration(array $config = []): self
    // {
    //     if (!empty($config)) {
    //         $this->configuration = $config;
    //     } else {
    //         $this->configuration = array_merge(
    //             $this->getFulfillmentMethod()->courierConfig(),
    //             $this->getShippingMethod()->courierConfig(null, $this->getFulfillmentMethod()->courier),
    //             $this->fulfillment->courierConfig(null, $this->getFulfillmentMethod()->courier)
    //         );
    //     }

    //     return $this;
    // }

    // /**
    //  * Get a fulfillment method from the fulfillment
    //  */
    // public function getFulfillmentMethod(): FulfillmentMethod
    // {
    //     return $this->fulfillment->method;
    // }

    // /**
    //  * Get a shipping method from the fulfillment
    //  */
    // public function getShippingMethod(): ShippingMethod
    // {
    //     return $this->fulfillment->items->first()->order->shippingMethod;
    // }

    // /**
    //  * Get the order for the fulfillment.
    //  */
    // public function order(): Order
    // {
    //     return $this->fulfillment->items()->first()->order;
    // }
}
