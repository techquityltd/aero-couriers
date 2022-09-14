<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Common\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierService extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'carrier',
        'service_type',
        'service_code',
    ];

    public function scopeDisplayAvailable($query)
    {
        return $query
            ->orderBy('carrier')
            ->cursor()
            ->mapToGroups(fn ($service) => [$service->carrier => $service])
            ->map(fn ($group) => $group->mapWithKeys(fn ($service) => [
                $service->id => $service->name ?? $service->description
            ]))
            ->toArray();
    }
}
