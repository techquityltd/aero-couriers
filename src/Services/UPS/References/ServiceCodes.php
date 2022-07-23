<?php

namespace Techquity\Aero\Couriers\Services\UPS\References;

class ServiceCodes
{
    public const DEFAULT = '11';
    public const TYPES = [
        '01' => 'Next Day Air',
        '02' => '2nd Day Air ',
        '03' => 'Ground',
        '07' => 'Express',
        '08' => 'Expedited',
        '11' => 'UPS Standard',
        '12' => '3 Day Select',
        '13' => 'Next Day Air Saver',
        '14' => 'UPS Next Day Air® Early',
        '17' => 'UPS Worldwide Economy DDU',
        '54' => 'Express Plus',
        '59' => '2nd Day Air A.M.',
        '65' => 'UPS Saver',
        'M2' => 'First Class Mail',
        'M3' => 'Priority Mail',
        'M4' => 'Expedited MaiI Innovations',
        'M5' => 'Priority Mail Innovations',
        'M6' => 'Economy Mail Innovations',
        'M7' => 'MaiI Innovations (MI) Returns',
        '70' => 'UPS Access PointTM Economy',
        '71' => 'UPS Worldwide Express Freight Midday',
        '72' => 'UPS Worldwide Economy',
        '74' => 'UPS Express®12:00',
        '82' => 'UPS Today Standard',
        '83' => 'UPS Today Dedicated Courier',
        '84' => 'UPS Today Intercity',
        '85' => 'UPS Today Express',
        '86' => 'UPS Today Express Saver',
        '96' => 'UPS Worldwide Express Freight.',
    ];
}
