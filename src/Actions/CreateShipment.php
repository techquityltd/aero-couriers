<?php

namespace Techquity\Aero\Couriers\Actions;

use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Support\Facades\Auth;
use Techquity\Aero\Couriers\Models\CourierConnector;
use Techquity\Aero\Couriers\Models\CourierService;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CreateShipment
{
    use UsesCourierDriver;

    /**
     * The fulfillment that owns the shipment.
     */
    protected Fulfillment $fulfillment;

    /**
     * The new courier shipment instance.
     */
    protected CourierShipment $shipment;

    /**
     * Determines if the shipments service was manually set.
     */
    protected bool $serviceManuallySet = false;

    /**
     * Determines if the shipments connector was manually set.
     */
    protected bool $connectorManuallySet = false;

    /**
     * Create a new Create Fulfillment instance.
     */
    public function __construct(Fulfillment $fulfillment)
    {
        $this->shipment = new CourierShipment();
        $this->shipment->admin()->associate(Auth::guard('admin')->user());
        $this->fulfillment = $fulfillment;
    }

    /**
     * Manually set the shipments courier service
     */
    public function usingService(CourierService $service): self
    {
        $this->shipment->courierService()->associate($service);

        $this->serviceManuallySet = true;

        return $this;
    }

    /**
     * Manually set the shipments courier connector
     */
    public function usingConnector(?CourierConnector $connector): self
    {
        $this->shipment->courierConnector()->associate($connector);

        $this->connectorManuallySet = true;

        return $this;
    }

    /**
     * Dynamically access the classes attributes.
     */
    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }

    /**
     * Save the shipment and fulfillment upon the object's destruction.
     */
    public function __destruct()
    {
        // Set the service if not manually set.
        if (!$this->serviceManuallySet) {
            $this->usingService($this->fulfillment->method->courierService);
        }
        // Set the connector if not manually set.
        if (!$this->connectorManuallySet) {
            $this->usingConnector($this->fulfillment->method->courierConnector);
        }

        $this->shipment->save();

        $this->fulfillment->courier_shipment_id = $this->shipment->id;
        $this->fulfillment->save();
    }
}
