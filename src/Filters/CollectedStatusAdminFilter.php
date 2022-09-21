<?php

namespace Techquity\Aero\Couriers\Filters;

use Aero\Admin\Filters\DropdownAdminFilter;

class CollectedStatusAdminFilter extends DropdownAdminFilter
{
    protected function handleDropdown($selected, $query)
    {
        switch ($selected) {
            case 'any':
                $query->whereHas('courierCollection')->orWhereDoesntHave('courierCollection');
                break;
            case 'collected':
                $query->whereHas('courierCollection');
                break;
        }
    }

    protected function dropdowns(): array
    {
        return [
            [
                'value' => '',
                'name' => 'Uncollected Only',
            ],
            [
                'value' => 'any',
                'name' => 'Any',
            ],
            [
                'value' => 'collected',
                'name' => 'Collected Only',
            ],
        ];
    }

    protected function baseName(): string
    {
        return 'CollectedStatus';
    }
}
