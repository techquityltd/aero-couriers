<?php

namespace Techquity\Aero\Couriers\Services\UPS\References;

class PackageTypes
{
    public const DEFAULT = 57;
    public const TYPES = [
        '01' => 'UPS Letter',
        '02' => 'Customer Supplied Package',
        '03' => 'Tube',
        '04' => 'PAK',
        '21' => 'UPS Express Box',
        '24' => 'UPS 25KG Box',
        '25' => 'UPS 10KG Box',
        '30' => 'Pallet',
        '2a' => 'Small Express Box',
        '2b' => 'Medium Express Box 2c =Large Express Box ',
        '56' => 'Flats',
        '57' => 'Parcels',
        '58' => 'BPM',
        '59' => 'First Class',
        '60' => 'Priority',
        '61' => 'Machineables',
        '62' => 'Irregulars',
        '63' => 'Parcel Post',
        '64' => 'BPM Parcel',
        '65' => 'Media Mail',
        '66' => 'BPM Flat',
        '67' => 'Standard Flat.',
    ];
}
