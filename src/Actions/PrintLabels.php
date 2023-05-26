<?php

namespace Techquity\Aero\Couriers\Actions;

use Aero\Admin\Models\Admin;
use Techquity\Aero\Couriers\Models\PendingLabel;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class PrintLabels
{
    use UsesCourierDriver;

    public function __invoke($shipments, Admin $admin)
    {
        $shipments
            ->groupBy(fn ($shipment) => $shipment->courierConnector->id)
            ->each(function ($shipments) use ($admin) {
                $driver = $this->getCourierDrivers()->get($shipments->first()->courierService->carrier);
                $driver = (new $driver())->setShipments($shipments);

                $driver->printLabels($admin);
            });

        return PendingLabel::count() ? true : back()->with(['error' => 'Unable to find labels for the selected shipments']);
    }
}
