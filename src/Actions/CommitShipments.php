<?php

namespace Techquity\Aero\Couriers\Actions;

use Aero\Admin\Models\Admin;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CommitShipments
{
    use UsesCourierDriver;

    public function __invoke($shipments, ?Admin $admin = null)
    {
        $shipments
            ->groupBy(fn ($shipment) => $shipment->courierConnector->id)
            ->each(function ($shipments) use ($admin) {
                $driver = $this->getCourierDrivers()->get($shipments->first()->courierService->carrier);
                $driver = (new $driver())->setShipments($shipments);

                if ($admin) {
                    $driver->labelsShouldAutoPrint($admin);
                }

                $driver->commit();
            });

        return true;
    }
}
