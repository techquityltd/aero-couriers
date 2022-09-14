<?php

namespace Techquity\Aero\Couriers\Actions;

use Aero\Fulfillment\Models\Fulfillment;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class DeleteFulfillment
{
    use UsesCourierDriver;

    public function __invoke(Fulfillment $fulfillment): bool
    {
        if ($fulfillment->courierShipment->committed) {
            $driver = $this->getCourierDrivers()->get($fulfillment->courierShipment->courierService->carrier);

            (new $driver())->setShipments(collect()->push($fulfillment->courierShipment))->cancel();

            if (!$fulfillment->courierShipment->cancelled) {
                return false;
            }
        }

        // Delete the fulfillment...
        $fulfillment->delete();

        // Check if the shipment should be deleted...
        if (!$fulfillment->courierShipment->fresh()->fulfillments->count())
        {
            $fulfillment->courierShipment->delete();
        }

        return true;
    }
}
