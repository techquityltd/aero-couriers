<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Admin\Models\Admin;
use Aero\Cart\Models\Order;
use Aero\Cart\Models\OrderStatus;
use Aero\Common\Models\Model;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CourierShipment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consignment_number',
        'committed',
        'request',
        'response',
        'failed',
        'cancelled',
        'failed_messages',
        'label',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'failed_messages' => 'array',
    ];

    /**
     * Get the fulfillments for the shipment.
     */
    public function fulfillments()
    {
        return $this->hasMany(Fulfillment::class)->with('items.order');
    }

    /**
     * Get the orders for the shipment.
     */
    public function getOrdersAttribute()
    {
        return $this->fulfillments->pluck('items.*.order')->flatten()->filter()->unique();
    }

    /**
     * Get the admin who created the shipment.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the connector for the shipment.
     */
    public function courierConnector()
    {
        return $this->belongsTo(CourierConnector::class);
    }

    /**
     * Get the service used for the shipment.
     */
    public function courierService()
    {
        return $this->belongsTo(CourierService::class);
    }

    /**
     * Get the collection used for the shipment.
     */
    public function courierCollection()
    {
        return $this->belongsTo(CourierCollection::class);
    }

    public function isFullyAllocated(): bool
    {
        return $this->orders->filter(fn (Order $order) => $order->isFullyAllocated)->count() === $this->orders->count();
    }

    public function isComplete(): bool
    {
        return (bool) $this->courierCollection;
    }

    /**
     * Mark the shipment as committed and update its relations.
     */
    public function markAsCommitted(string $consignmentNumber, ?string $trackingNumber = '', ?string $trackingUrl = '', ?string $label = ''): self
    {
        $this->update([
            'consignment_number' => $consignmentNumber,
            'label' => $label,
            'committed' => true,
        ]);

        $this->fulfillments->each->update([
            'tracking_code' => $trackingNumber,
            'tracking_url' => $trackingUrl,
            'state' => Fulfillment::SUCCESSFUL
        ]);

        // FIRE EVENT

        return $this;
    }

    /**
     * Mark the shipment as failed and trigger required events.
     */
    public function markAsFailed($messages): self
    {
        $this->fulfillments->each->update(['state' => Fulfillment::FAILED]);

        $this->updateFailedMessages($messages);

        $this->update([
            'failed' => true,
        ]);

        // FIRE EVENT

        return $this;
    }

    /**
     * Mark the shipment as cancelled.
     */
    public function markAsCancelled(): self
    {
        $this->fulfillments->each->update(['state' => Fulfillment::CANCELED]);

        $this->update([
            'cancelled' => true,
        ]);

        // FIRE EVENT

        return $this;
    }

    public function updateFailedMessages($messages)
    {
        if (is_string($messages)) {
            $messages = array($messages);
        }

        $this->update([
            'failed' => true,
            'failed_messages' => $messages
        ]);
    }
}
