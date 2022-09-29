<?php

namespace Techquity\Aero\Couriers\ResourceLists;

use Aero\Admin\ResourceLists\AbstractResourceList;
use Aero\Admin\ResourceLists\ResourceListColumn;
use Aero\Admin\Traits\IsExtendable;
use Illuminate\Routing\Route;
use Techquity\Aero\Couriers\Filters\CarrierAdminFilter;
use Techquity\Aero\Couriers\Models\CourierConnector;
use Illuminate\Support\Str;

class CourierConnectorsResourceList extends AbstractResourceList
{
    use IsExtendable;

    protected $headerSlot = 'couriers.shipments.connectors.header.buttons';

    protected $filters = [
        CarrierAdminFilter::class
    ];

    protected $selected;

    public function __construct(CourierConnector $connector)
    {
        $this->resource = $connector;
        $this->selected = optional(app(Route::class)->parameter('connector'))->id;
    }

    protected function columns(): array
    {
        return [
            ResourceListColumn::create('Name', function ($row) {
                return $row->name;
            }),
            ResourceListColumn::create('Carrier', function ($row) {
                return $row->carrier;
            }),
            ResourceListColumn::create('', function ($row) {
                return $row->url;
            }),
            ResourceListColumn::create('', function ($row) {
                return $row->user;
            }),
            ResourceListColumn::create('', function ($row) {
                $limit = round(strlen($row->token) / 2);

                $firstHalf = Str::substr($row->token, 0, $limit);
                $firstHalf = preg_replace("/[^-]/i", "X", $firstHalf);

                $lastHalf = Str::substr($row->token, $limit);

                return $firstHalf . $lastHalf;
            }),
            ResourceListColumn::create('', function ($row) {
                if ($row->id === $this->selected) {
                    return view('couriers::resource-lists.cancel-link', [
                        'route' => route('admin.courier-manager.connectors.index', array_merge(request()->all())),
                    ])->render();
                } else {
                    return view('admin::resource-lists.manage-link', [
                        'route' => route('admin.courier-manager.connectors.edit', array_merge(request()->all(), ['connector' => $row])),
                    ])->render();
                }
            }, 'action'),
        ];
    }

    protected function handleSearch($search)
    {
        $this->query->where(function ($query) use ($search) {
            $query->whereLower('name', 'like', "%{$search}%")
                ->orWhereLower('carrier', 'like', "%{$search}%")
                ->orWhereLower('user', 'like', "%{$search}%");
        });
    }

    public function backButtonLink()
    {
        return route('admin.courier-manager.shipments.index');
    }

    public function title(): string
    {
        return 'Courier Connectors';
    }
}
