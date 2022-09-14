<?php

namespace Techquity\Aero\Couriers\Filters;

use Aero\Admin\Filters\DropdownAdminFilter;

class CommittedStatusAdminFilter extends DropdownAdminFilter
{
    protected function handleDropdown($selected, $query)
    {
        if ($selected) {
            $query->where('committed', $selected === 'committed');
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
                'name' => 'Uncommitted Only',
            ],
            [
                'value' => 'committed',
                'name' => 'Committed Only',
            ],
        ];
    }

    protected function baseName(): string
    {
        return 'CommittedStatus';
    }
}
