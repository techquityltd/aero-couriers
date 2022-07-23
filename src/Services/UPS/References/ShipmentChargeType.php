<?php

namespace Techquity\Aero\Couriers\Services\UPS\References;

class ShipmentChargeType
{
    public const DEFAULT = 01;
    public const TYPES = [
        '01' => 'Transportation',
        '02' => 'Duties and Taxes',
        '03' => 'Broker of Choice',
    ];
}
