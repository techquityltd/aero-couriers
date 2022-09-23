<?php

namespace Techquity\Aero\Couriers\Abstracts;

use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Models\FulfillmentAddress;
use Aero\Fulfillment\Models\FulfillmentMethod;
use Techquity\Aero\Couriers\Contracts\ShipmentFactory;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\Traits\UsesShipmentFactory;

class AbstractShipmentFactory implements ShipmentFactory
{
    use UsesShipmentFactory;

    protected CourierShipment $shipment;

    protected FulfillmentAddress $address;

    protected FulfillmentMethod $fulfillmentMethod;

    public function __construct(CourierShipment $shipment)
    {
        $this->shipment = $shipment;
        $this->address = $this->shipment->fulfillments->first()->address;
        $this->fulfillmentMethod = $this->shipment->fulfillments()->first()->method;
    }

    public function recipient(string $key, ?string $default = null)
    {
        // Check for the key in address before searching in fulfillments.
        return data_get(
            $this->address,
            $key,
            $this->shipment->fulfillments->pluck($key)->unique()->first()
        );
    }

    public function service(string $key, ?string $default = null)
    {
        return data_get($this->shipment->courierService, $key, $default);
    }

    public function additional(string $key, $default = null)
    {
        $additional = $this->fulfillmentMethod->additional($key);

        return isset($additional) ? $additional : $default;
    }

    public function connector(string $key, ?string $default = null)
    {
        return data_get($this->shipment->courierConnector, $key, $default);
    }

    /**
     * Get the weight of an individual fulfillment.
     */
    public function fulfillmentWeight(Fulfillment $fulfillment, $unit)
    {
        return $this->convertWeight($fulfillment->weight, $unit);
    }

    /**
     * Get the total weight of the shipment.
     */
    protected function totalWeight(string $unit = 'g'): float
    {
        return $this->convertWeight(
            $this->shipment->fulfillments->sum('weight'),
            $unit
        );
    }

    public function convertWeight(int $weight, string $unit): float
    {
        switch (strtolower($unit)) {
            case 'kg':
                return $weight / 1000;
            default:
                return $weight;
        }
    }

    public function make(): array
    {
        return [];
    }
}
