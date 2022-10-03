<?php

namespace Techquity\Aero\Couriers\ResourceLists;

use Aero\Admin\ResourceLists\AbstractResourceList;
use Aero\Admin\ResourceLists\ResourceListColumn;
use Aero\Admin\Traits\IsExtendable;
use Techquity\Aero\Couriers\Models\CourierCollection;

class CourierCollectionsResourceList extends AbstractResourceList
{
    use IsExtendable;

    protected $headerSlot = 'couriers.shipments.connectors.header.buttons';

    public function __construct(CourierCollection $collection)
    {
        $this->resource = $collection;
    }

    protected function columns(): array
    {
        return [
            ResourceListColumn::create('Connector', function ($row) {
                return $row->shipments()->first()->courierConnector->name;
            }),
            ResourceListColumn::create('Carrier', function ($row) {
                return $row->shipments()->first()->courierService->carrier;
            }),
            ResourceListColumn::create('From Date', function ($row) {
                return $row->shipments()->oldest()->first()->created_at;
            }),
            ResourceListColumn::create('To Date', function ($row) {
                return $row->shipments()->latest()->first()->created_at;
            }),
            ResourceListColumn::create('Shipments', function ($row) {
                return $row->shipments()->count();
            }),
            ResourceListColumn::create('Admin', function ($row) {
                if (!$row->admin) {
                    return '';
                }

                return view('admin::resource-lists.link', [
                    'route' => route('admin.configuration.users.edit', array_merge(request()->all(), ['user' => $row->admin->id])),
                    'text' => $row->admin->name,
                ])->render();
            }),
            ResourceListColumn::create('', function ($row) {
                if (strlen($row->manifest) < 3) {
                    return '';
                }

                return view('gfs::resource-lists.links.download', [
                    'key' => $row->id,
                    'route' => route('admin.courier-manager.collections.manifest', array_merge(request()->all(), ['collection' => $row])),
                ])->render();
            }, 'action'),
        ];
    }

    protected function newQuery()
    {
        return $this->resource->newQuery()->whereHas('shipments');
    }

    public function backButtonLink()
    {
        return route('admin.courier-manager.shipments.index');
    }

    public function title(): string
    {
        return 'Courier Collections';
    }
}
