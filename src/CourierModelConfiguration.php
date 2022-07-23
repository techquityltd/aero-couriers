<?php

namespace Techquity\Aero\Couriers;

use Aero\Common\Models\Model;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Aero\Fulfillment\FulfillmentProcessor;
use Illuminate\Support\Collection;

class CourierModelConfiguration
{
    protected Collection $configuration;
    protected Fulfillment $fulfillment;
    protected FulfillmentMethod $fulfillmentMethod;
    protected $driver;

    public function __construct(Collection $configuration, Model $model)
    {
        $this->configuration = $configuration;

        if ($model instanceof Fulfillment) {
            $this->fulfillment = $model;
            $this->fulfillmentMethod = $model->method;
        } elseif ($model instanceof FulfillmentMethod) {
            $this->fulfillmentMethod = $model;
        }

        $drivers = FulfillmentProcessor::getDrivers();

        $this->driver = $drivers[$this->fulfillmentMethod->driver];
    }

    public function toArray()
    {
        return $this->all()->toArray();
    }

    public function all()
    {
        return $this->configuration;
    }

    public function __get($name)
    {
        // First we check the fulfillment...
        if ($this->fulfillment) {
            $value = data_get($this->configuration, $name);

            // If no value we do this process again but this time checking the method...
            return $value ?: $this->fulfillmentMethod->courier_configuration->$name;
        }

        // Being here we do not have the fulfillment but only the method...
        $value = data_get($this->configuration, $name);

        // If no value we revert to the driver settings...
        return $value ?: setting($this->driver::SETTINGS . ".{$name}");
    }
}
