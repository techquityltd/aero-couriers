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
        'group',
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
            ->map(function ($group) {
                return $group
                    ->sortBy('service_code')
                    ->mapToGroups(fn ($service) => [$service->group ?? 'Standard' => $service])
                    ->map(function ($group) {
                        return $group
                            ->mapWithKeys(function ($service) {
                                $name = $service->name ?? $service->description;
                                return [$service->id => $service->service_code . ' - ' . $name];
                            });
                    })
                    ->sortKeys();
            })
            ->toArray();
    }
}
