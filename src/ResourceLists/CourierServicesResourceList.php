<?php

namespace Techquity\Aero\Couriers\ResourceLists;

use Aero\Admin\ResourceLists\AbstractResourceList;
use Aero\Admin\ResourceLists\ResourceListColumn;
use Aero\Admin\ResourceLists\ResourceListSortBy;
use Aero\Admin\Traits\IsExtendable;
use Illuminate\Routing\Route;
use Techquity\Aero\Couriers\Filters\CarrierAdminFilter;
use Techquity\Aero\Couriers\Models\CourierService;

class CourierServicesResourceList extends AbstractResourceList
{
    use IsExtendable;

    protected $headerSlot = 'couriers.services.index.header.buttons';

    protected $filters = [
        CarrierAdminFilter::class
    ];

    protected $selected;

    public function __construct(CourierService $service)
    {
        $this->resource = $service;
        $this->selected = optional(app(Route::class)->parameter('service'))->id;
    }

    protected function columns(): array
    {
        return [
            ResourceListColumn::create('Display Name', function ($row) {
                return $row->name;
            }),
            ResourceListColumn::create('Description', function ($row) {
                return $row->description;
            }),
            ResourceListColumn::create('Group', function ($row) {
                return $row->courierServiceGroup ? $row->courierServiceGroup->name : '';
            }),
            ResourceListColumn::create('Carrier', function ($row) {
                return $row->carrier;
            }),
            ResourceListColumn::create('Service Code', function ($row) {
                return $row->service_code;
            }),
            ResourceListColumn::create('Service Type', function ($row) {
                return $row->service_type;
            }),
            ResourceListColumn::create('', function ($row) {
                if (!array_key_exists('deleted', $this->parameters)) {
                    if ($row->id === $this->selected) {
                        return view('couriers::resource-lists.cancel-link', [
                            'route' => route('admin.courier-manager.services.index', array_merge(request()->all())),
                        ])->render();
                    } else {
                        return view('admin::resource-lists.manage-link', [
                            'route' => route('admin.courier-manager.services.edit', array_merge(request()->all(), ['service' => $row])),
                        ])->render();
                    }
                }
            }, 'action'),
        ];
    }

    protected function sortBys(): array
    {
        return [
            ResourceListSortBy::create(null, function ($_, $query) {
                return $query->orderBy('carrier');
            }),

            ResourceListSortBy::create([
                'name-az' => 'Name A to Z',
                'name-za' => 'Name Z to A',
            ], function ($sortBy, $query) {
                return $sortBy === 'name-az' ? $query->orderBy('name') : $query->orderByDesc('name');
            }),

            ResourceListSortBy::create([
                'saturday-az' => 'Saturday Delivery',
            ], function ($sortBy, $query) {
                return $sortBy === 'saturday-az' ? $query->orderByDesc('saturday_delivery') : $query->orderBy('saturday_delivery');
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
            $query->whereLower('description', 'like', "%{$search}%")
                ->orWhereLower('service_type', 'like', "%{$search}%")
                ->orWhereLower('service_code', 'like', "%{$search}%")
                ->orWhereLower('carrier', 'like', "%{$search}%");
        });
    }

    public function backButtonLink()
    {
        if (array_key_exists('deleted', $this->parameters)) {
            return route('admin.courier-manager.services.index');
        }

        return route('admin.courier-manager.shipments.index');
    }

    public function title(): string
    {
        return 'Courier Services';
    }
}
