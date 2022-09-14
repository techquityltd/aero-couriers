<?php

namespace Techquity\Aero\Couriers\Filters;

use Aero\Admin\Filters\DropdownAdminFilter;

class CollectedStatusAdminFilter extends DropdownAdminFilter
{
    protected function handleDropdown($selected, $query)
    {
        if ($selected) {
            $query->whereHas('collection', fn($q) => $q->where('collected', $selected === 'collected'));
        }
    }

    protected function dropdowns(): array
    {
        return [
            [
                'value' => '',
                'name' => 'Any',
            ],
            [
                'value' => 'exclude',
                'name' => 'Uncollected Only',
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
