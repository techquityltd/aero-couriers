<?php

namespace Techquity\Aero\Couriers;

use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Techquity\Aero\Couriers\Services\AbstractCourierDriver;
use Techquity\Aero\Couriers\Services\FedEx\FedExDriver;

class CourierManager
{
    /**
     * The container instance.
     */
    protected Container $container;

    /**
     * The array of available "drivers".
     */
    protected array $drivers = [];

    /**
     * Create a new courier manager instance.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->drivers = [
            FedExDriver::NAME => fn () => new FedExDriver()
        ];
    }

    /**
     * Get all of the available "drivers".
     */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * Get a new instance of the selected driver.
     */
    public function getDriver(string $driver): AbstractCourierDriver
    {
        if (array_key_exists($driver, $this->drivers)) {
            return $this->drivers[$driver]();
        }

        // throw something
    }

    /**
     * Get the configuration for each fulfillment.
     */
    public function getFulfillmentConfiguration($courier)
    {
        return collect($this->drivers)->get($courier)()->fulfillmentConfiguration();
    }

    /**
     * Get the configuration for each couriers fulfillment method.
     */
    public function getFulfillmentMethodConfiguration(): array
    {
        return collect($this->drivers)
            ->mapWithKeys(fn ($driver, $key) => [$key => $driver()->fulfillmentMethodConfiguration()])
            ->toArray();
    }

    /**
     * Get the configuration for each couriers shipping method.
     */
    public function getShippingMethodConfiguration(): array
    {
        return collect($this->drivers)
            ->mapWithKeys(fn ($driver, $key) => [$key => $driver()->shippingMethodConfiguration()])
            ->toArray();
    }

    /**
     * Get courier driver with the fulfillment attached.
     */
    public function forFulfillment(Fulfillment $fulfillment): AbstractCourierDriver
    {
        return $this->getDriver($fulfillment->method->courier)->attachFulfillment($fulfillment);
    }
}
