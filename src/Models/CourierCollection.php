<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Admin\Models\Admin;
use Aero\Common\Models\Model;

class CourierCollection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'manifest',
        'admin_id'
    ];

    /**
     * Get the shipments for the collection.
     */
    public function shipments()
    {
        return $this->hasMany(CourierShipment::class);
    }

    /**
     * Get the admin who created the collection.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
