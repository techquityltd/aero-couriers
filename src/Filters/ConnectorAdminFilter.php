<?php

namespace Techquity\Aero\Couriers\Filters;

use Aero\Admin\Filters\CheckboxListAdminFilter;
use Techquity\Aero\Couriers\Models\CourierConnector;

class ConnectorAdminFilter extends CheckboxListAdminFilter
{
    protected function handleCheckboxList(array $selected, $query)
    {
        $query->whereIn('couriers_connector_id', $selected);
    }

    protected function checkboxes(): array
    {
        return CourierConnector::all()->map(function ($connector) {
            return [
                'id' => $connector->id,
                'name' => ucwords($connector->name),
                'url' => $this->getUrlFor($connector->id),
            ];
        })->toArray();
    }

    public function baseName(): string
    {
        return 'Connector';
    }
}
