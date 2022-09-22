<?php

namespace Techquity\Aero\Couriers\Abstracts;

use Techquity\Aero\Couriers\Models\CourierShipment;

class AbstractShipmentEvent
{
    public $shipment;

    public function __construct(CourierShipment $shipment)
    {
        $this->shipment = $shipment;
    }
}
