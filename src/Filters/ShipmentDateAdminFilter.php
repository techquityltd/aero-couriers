<?php

namespace Techquity\Aero\Couriers\Filters;

use Aero\Admin\Filters\OptionsDateRangeAdminFilter;

class ShipmentDateAdminFilter extends OptionsDateRangeAdminFilter
{
    protected function handleDateRange($startDate, $endDate, $query)
    {
        $query->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        });
    }

    protected function baseName(): string
    {
        return 'ShipmentDate';
    }
}
