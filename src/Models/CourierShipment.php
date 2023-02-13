<?php

namespace Techquity\Aero\Couriers\Models;

use Aero\Admin\Models\Admin;
use Aero\Cart\Models\Order;
use Aero\Common\Models\Model;
use Illuminate\Support\Str;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Techquity\Aero\Couriers\Abstracts\AbstractResponse;
use Techquity\Aero\Couriers\Events\ShipmentCanceled;
use Techquity\Aero\Couriers\Events\ShipmentCollected;
use Techquity\Aero\Couriers\Events\ShipmentCommitted;
use Techquity\Aero\Couriers\Events\ShipmentFailed;
use Techquity\Aero\Couriers\Traits\InteractsWithFulfillmentDriver;

class CourierShipment extends Model
{
    use SoftDeletes;
    use InteractsWithFulfillmentDriver;

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

    /**
     * Determine if the shipments orders are fully allocated.
     */
    public function isFullyAllocated(): bool
    {
        return $this->orders->filter(fn (Order $order) => $order->isFullyAllocated)->count() === $this->orders->count();
    }

    /**
     * Determine if the shipment has been committed and collected.
     */
    public function isComplete(): bool
    {
        return (bool) $this->courierCollection;
    }

    /**
     * Get the fulfillment driver used for the shipment.
     */
    public function getDriverAttribute()
    {
        if ($this->courierService) {
            return $this->getCourierDrivers($this->courierService->carrier);
        }
    }

    /**
     * Mark the shipment as committed and update its relations.
     */
    public function markAsCommitted(?AbstractResponse $response = null): self
    {
        $this->update([
            'consignment_number' => ($response ? $response->getConsignmentNumber() : null) ?? Str::random(),
            'label' => ($response ? $response->getLabel() : null) ?? null,
            'committed' => true,
        ]);

        $this->fulfillments->each->update([
            'tracking_code' => ($response ? $response->getTrackingNumber() : null) ?? '',
            'tracking_url' => ($response ? $response->getTrackingUrl() : null) ?? '',
            'state' => Fulfillment::SUCCESSFUL
        ]);

        $this->orders->each(function (Order $order) {
            $order->setOrderStatus($this->driver::determineOrderStatus($order, $this));
        });

        event(new ShipmentCommitted($this));

        return $this;
    }

    /**
     * Mark the shipment as failed and trigger required events.
     */
    public function markAsFailed($messages = null): self
    {
        if ($messages instanceof AbstractResponse) {
            $messages = $messages->getFailedMessages();
        }

        $this->updateFailedMessages($messages);

        $this->fulfillments->each->update(['state' => Fulfillment::FAILED]);

        $this->orders->each(function (Order $order) {
            $order->setOrderStatus($this->driver::determineOrderStatus($order, $this));
        });

        event(new ShipmentFailed($this));

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

        $this->orders->each(function (Order $order) {
            $order->setOrderStatus($this->driver::determineOrderStatus($order, $this));
        });

        event(new ShipmentCanceled($this));

        return $this;
    }

    /**
     * Mark the shipment as collected.
     */
    public function markAsCollected(CourierCollection $courierCollection): self
    {
        $this->courierCollection()->associate($courierCollection);
        $this->save();

        $this->fulfillments->each->update(['state' => Fulfillment::SUCCESSFUL]);

        $this->orders->each(function (Order $order) {
            $order->setOrderStatus($this->driver::determineOrderStatus($order, $this));
        });

        event(new ShipmentCollected($this));

        return $this;
    }

    /**
     * Save the error messages to the shipment.
     */
    public function updateFailedMessages($messages): void
    {
        if (is_string($messages)) {
            $messages = array($messages);
        }

        $this->update([
            'failed' => true,
            'failed_messages' => $messages
        ]);
    }

    /**
     * Save the request and response data.
     */
    public function saveResponse(AbstractResponse $response): self
    {
        $this->update([
            'request' => json_encode($response->request()),
            'response' => json_encode($response->original()),
        ]);

        return $this;
    }

    public function getIsCsvResponseAttribute()
    {
        return method_exists($this->driver, 'generateCsv');
    }
}
