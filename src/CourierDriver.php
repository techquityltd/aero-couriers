<?php

namespace Techquity\Aero\Couriers;

use Aero\Admin\Models\Admin;
use Aero\Cart\Models\Order;
use Aero\Cart\Models\OrderStatus;
use Aero\Fulfillment\FulfillmentDriver;
use Aero\Fulfillment\Models\Fulfillment;
use Aero\Fulfillment\Responses\FulfillmentResponse;
use Illuminate\Support\Collection;
use Techquity\Aero\Couriers\Actions\CommitShipments;
use Techquity\Aero\Couriers\Models\CourierCollection;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\Models\PendingLabel;
use Illuminate\Database\Eloquent;

class CourierDriver extends FulfillmentDriver
{
    /**
     * The shipments.
     */
    protected $shipments;

    protected $admin;

    protected $autoPrintLabels = false;

    /**
     * The queue courier jobs should use.
     */
    public static string $queue = 'couriers';

    /**
     * The default fulfillment state of a fulfillment when created.
     *
     * @return string
     */
    public function getDefaultState(): string
    {
        return Fulfillment::OPEN;
    }

    /**
     * Set the shipments collection.
     */
    public function setShipments($shipments): self
    {
        $this->shipments = ($shipments instanceof Model ? collect([$shipments]) : $shipments);

        return $this;
    }

    public function labelsShouldAutoPrint(Admin $admin): self
    {
        $this->admin = $admin;
        $this->autoPrintLabels = true;

        return $this;
    }

    /**
     * Commit the shipments to the courier.
     */
    public function commit(): void
    {
        $this->shipments->each(function (CourierShipment $shipment) {
            $shipment->markAsCommitted();
        });
    }

    /**
     * Cancel a committed shipment.
     */
    public function cancel(): void
    {
        $this->shipments->each(function (CourierShipment $shipment) {
            $shipment->markAsCancelled();
        });
    }

    /**
     * Create a new return shipment
     */
    public function return(): void
    {
        /**
         * TODO:
         * - Add returns resource list
         * - Generate returns from the shipment
         * - Add option to print a return label with a shipment
         */
    }

    /**
     * Trigger a shipment as collected.
     */
    public function collect(): void
    {
        /**
         * The idea here is you would create a manifest that can be printed in order to
         * check off and have the courier sign what he is taking. For now we create a new collection
         * and mark as collected.
         */
        $collection = CourierCollection::create();

        $this->shipments->each(function (CourierShipment $shipment) use ($collection) {
            $shipment->courierCollection()->associate($collection);
            $shipment->save();

            $shipment->orders->each(fn ($order) => $this->determineOrderStatus($order, $shipment));
        });
    }

    /**
     * Get the the tracking url.
     */
    protected function getTrackingUrl(string $trackingCode): string
    {
        return '';
    }

    /**
     * Determine what status the order should be.
     */
    public static function determineOrderStatus(Order $order, CourierShipment $shipment): OrderStatus
    {
        if ($shipment->isComplete()) {
            return OrderStatus::forState(OrderStatus::COMPLETE)->first();
        }

        if ($shipment->committed) {
            return OrderStatus::forState(OrderStatus::DISPATCHED)->first();
        }

        if ($shipment->failed) {
            return OrderStatus::forState(OrderStatus::PROCESSING)->first();
        }
    }

    /**
     * Determine what status the fulfilment should be.
     */
    public static function determineFulfillmentStatus(): string
    {
        return Fulfillment::SUCCESSFUL;
    }

    /**
     * Make the fulfillment request.
     *
     * @return \Aero\Fulfillment\Contracts\Response
     */
    public function handle(): FulfillmentResponse
    {
        $this->fulfillments
            ->reject(fn (Fulfillment $fulfillment) => $fulfillment->courierShipment->committed)
            ->each(function (Fulfillment $fulfillment) {
                (new CommitShipments())(collect([$fulfillment->courierShipment]), $this->admin);
            });

        $response = new FulfillmentResponse();

        $response->setSuccessful(true);

        return $response;
    }

    /**
     * Get a collection of available services.
     */
    public function getServices(): Collection
    {
        return new Collection([]);
    }

    public function getOrder(): ?Order
    {
        if ($this->fulfillments) {
            return $this->fulfillments->first()->fresh()->items->first()->order ?? null;
        }
    }

    /**
     * Get all fulfillments on the order not belonging to the selected shipment.
     */
    public function otherFulfillments($shipment)
    {
        return $shipment->orders
            ->map(fn (Order $order) => $order->fulfillments)
            ->flatten()
            ->reject(fn (Fulfillment $fulfillment) => $fulfillment->courierShipment->is($shipment));
    }

    public function __destruct()
    {
        if ($this->autoPrintLabels) {

            $this->shipments->groupBy('courier_connector_id')->each(function ($shipments) {

                $firstShipment = $shipments->first();

                if ($firstShipment->isCsvResponse) {
                    $driver = (new $firstShipment->driver())->setShipments($shipments);
                    $fileName = $driver->generateCsvFileName();
                    $driver->generateCsv()->store($fileName);

                    (new PendingLabel([
                        'label' => $fileName,
                        'admin_id' => $this->admin->id
                    ]))->save();

                } else {
                    $shipments->each(function ($shipment) {
                        if ($shipment->label) {
                            (new PendingLabel([
                                'label' => $shipment->label,
                                'admin_id' => $this->admin->id
                            ]))->save();
                        }
                    });
                }
            });
        }
    }
}
