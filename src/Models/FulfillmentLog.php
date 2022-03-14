<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Admin\Models\Admin;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Database\Eloquent\Model;

class FulfillmentLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'message', 'type', 'data'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * The log type when the fulfillment has failed.
     */
    public const ERROR = 'error';

    /**
     * The log type when the fulfillment was successful.
     */
    public const SUCCESS = 'success';

    /**
     * The information log type for a fulfillment.
     */
    public const INFO = 'info';

    /**
     * The admin user that changed the Order Status.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * The fulfillment that this log belongs to.
     */
    public function fulfillment()
    {
        return $this->belongsTo(Fulfillment::class, 'fulfillment_batch_id');
    }
}
