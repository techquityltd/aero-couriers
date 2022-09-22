<?php

namespace Techquity\Aero\Couriers\Contracts;

interface ShipmentFactory
{
    /**
     * Make a new formatted courier shipment.
     */
    public function make(): array;
}
