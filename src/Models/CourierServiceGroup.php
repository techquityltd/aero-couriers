<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Common\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierServiceGroup extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sort',
    ];
}
