<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class PendingLabel extends Model
{
    protected $fillable = [
        'admin_id',
        'label',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
