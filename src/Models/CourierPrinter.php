<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Common\Models\Model;

class CourierPrinter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'host',
        'port',
        'auto_print'
    ];

    public function scopeDisplayAvailable($query)
    {
        return $query
            ->cursor()
            ->mapWithKeys(fn ($printer) => [
                $printer->id => $printer->name ?? $printer->description
            ])
            ->toArray();
    }
}
