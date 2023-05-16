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
        'courier_service_group_id',
        'carrier',
        'service_type',
        'service_code',
    ];

    public function courierServiceGroup()
    {
        return $this->belongsTo(CourierServiceGroup::class);
    }

    public function scopeDisplayAvailable($query)
    {
        return $query
            ->orderBy('carrier')
            ->cursor()
            ->mapToGroups(fn ($service) => [$service->carrier => $service])
            ->map(function ($group) {
                return $group
                    ->sortBy('service_code')
                    ->mapToGroups(fn ($service) => [$service->courierServiceGroup ? $service->courierServiceGroup->sort : '0' => $service])
                    ->sortKeys()
                    ->flatten()
                    ->mapToGroups(fn ($service) => [$service->courierServiceGroup ? $service->courierServiceGroup->name : 'Standard' => $service])
                    ->map(function ($group) {
                        return $group
                            ->mapWithKeys(function ($service) {
                                $name = $service->name ?? $service->description;
                                return [$service->id => $service->service_code . ' - ' . $name];
                            });
                    });
            })
            ->toArray();
    }
}
