<?php

namespace Techquity\Aero\Couriers\Services\FedEx\References;

class PickupTypes
{
    public const DEFAULT = 'USE_SCHEDULED_PICKUP';
    public const TYPES = [
        'CONTACT_FEDEX TO_SCHEDULE',
        'DROPOFF_AT_FEDEX_LOCATION',
        'USE_SCHEDULED_PICKUP'
    ];
}
