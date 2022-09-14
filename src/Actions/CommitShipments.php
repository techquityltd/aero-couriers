<?php

namespace Techquity\Aero\Couriers\Actions;

use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CommitShipments
{
    use UsesCourierDriver;

    public function __invoke($shipments)
    {
        $driver = $this->getCourierDrivers()->get($shipments->first()->courierService->carrier);

        (new $driver())->setShipments($shipments)->commit();
    }
}
