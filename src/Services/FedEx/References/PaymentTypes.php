<?php

namespace Techquity\Aero\Couriers\Services\FedEx\References;

class PaymentTypes
{
    public const DEFAULT = 'SENDER';
    public const TYPES = [
        'SENDER',
        'RECIPIENT',
        'THIRD_PARTY',
        'COLLECT'
    ];
}
