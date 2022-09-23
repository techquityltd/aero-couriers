<?php

namespace Techquity\Aero\Couriers\Actions;

use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CollectShipments
{
    use UsesCourierDriver;

    public function __invoke($shipments)
    {
        $shipments->groupBy(fn ($shipment) => $shipment->courierConnector->id)->each(function ($shipments) {
            $driver = $this->getCourierDrivers()->get($shipments->first()->courierService->carrier);

            (new $driver())->setShipments($shipments)->collect();
        });

        return true;
    }
}
