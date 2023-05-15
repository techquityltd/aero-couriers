<?php

namespace Techquity\Aero\Couriers\ResourceLists;

use Aero\Admin\ResourceLists\AbstractResourceList;
use Aero\Admin\ResourceLists\ResourceListColumn;
use Aero\Admin\ResourceLists\ResourceListSortBy;
use Aero\Admin\Traits\IsExtendable;
use Illuminate\Routing\Route;
use Techquity\Aero\Couriers\Filters\CarrierAdminFilter;
use Techquity\Aero\Couriers\Models\CourierService;
use Techquity\Aero\Couriers\Models\CourierServiceGroup;

class CourierServiceGroupsResourceList extends AbstractResourceList
{
    use IsExtendable;

    protected $headerSlot = 'couriers.services.index.header.buttons';

    protected $selected;

    public function __construct(CourierServiceGroup $group)
    {
        $this->resource = $group;
        $this->selected = optional(app(Route::class)->parameter('group'))->id;
    }

    protected function columns(): array
    {
        return [
            ResourceListColumn::create('Name', function ($row) {
                return $row->name;
            }),
            ResourceListColumn::create('Sort', function ($row) {
                return $row->sort;
            }),
            ResourceListColumn::create('', function ($row) {
                if ($row->id === $this->selected) {
                    return view('couriers::resource-lists.cancel-link', [
                        'route' => route('admin.courier-manager.service-groups.index', array_merge(request()->all())),
                    ])->render();
                } else {
                    return view('admin::resource-lists.manage-link', [
                        'route' => route('admin.courier-manager.service-groups.edit', array_merge(request()->all(), ['group' => $row])),
                    ])->render();
                }
            }, 'action'),
        ];
    }

    protected function sortBys(): array
    {
        return [
            ResourceListSortBy::create(null, function ($_, $query) {
                return $query->orderBy('sort');
            }),

            ResourceListSortBy::create([
                'name-az' => 'Name A to Z',
                'name-za' => 'Name Z to A',
            ], function ($sortBy, $query) {
                return $sortBy === 'name-az' ? $query->orderBy('name') : $query->orderByDesc('name');
            }),
        ];
    }

    protected function newQuery()
    {
        if (array_key_exists('deleted', $this->parameters)) {
            return $this->resource->newQuery()->withTrashed()->whereNotNull('deleted_at');
        }

        return $this->resource->newQuery();
    }

    protected function handleSearch($search)
    {
        $this->query->where(function ($query) use ($search) {
            $query->whereLower('name', 'like', "%{$search}%");
        });
    }

    public function backButtonLink()
    {
        return route('admin.courier-manager.services.index');
    }

    public function title(): string
    {
        return 'Courier Service Groups';
    }
}
