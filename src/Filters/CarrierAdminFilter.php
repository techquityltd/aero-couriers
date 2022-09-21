<?php

namespace Techquity\Aero\Couriers\Filters;

use Aero\Admin\Filters\CheckboxListAdminFilter;
use Illuminate\Support\Str;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CarrierAdminFilter extends CheckboxListAdminFilter
{
    use UsesCourierDriver;

    protected function handleCheckboxList(array $selected, $query)
    {
        $query->whereIn('carrier', $selected);
    }

    protected function checkboxes(): array
    {
        return $this->getCourierDrivers()->keys()->map(fn ($method) => [
            'id' => $method,
            'name' => ucwords($method),
            'url' => $this->getUrlFor($method),
        ])->toArray();
    }

    public function baseName(): string
    {
        return 'Carrier';
    }
}
