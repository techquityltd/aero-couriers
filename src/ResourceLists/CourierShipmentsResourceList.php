<?php

namespace Techquity\Aero\Couriers\ResourceLists;

use Aero\Admin\ResourceLists\AbstractResourceList;
use Aero\Admin\ResourceLists\ResourceListColumn;
use Aero\Admin\ResourceLists\ResourceListSortBy;
use Aero\Admin\Traits\IsExtendable;
use Techquity\Aero\Couriers\Filters\CollectedStatusAdminFilter;
use Techquity\Aero\Couriers\Filters\CommittedStatusAdminFilter;
use Techquity\Aero\Couriers\Filters\ConnectorAdminFilter;
use Techquity\Aero\Couriers\Filters\ShipmentDateAdminFilter;
use Techquity\Aero\Couriers\Models\CourierShipment;

class CourierShipmentsResourceList extends AbstractResourceList
{
    use IsExtendable;

    protected $headerSlot = 'couriers.shipments.index.header.buttons';

    protected $filters = [
        CommittedStatusAdminFilter::class,
        CollectedStatusAdminFilter::class,
        ConnectorAdminFilter::class,
        ShipmentDateAdminFilter::class,
    ];

    public function __construct(CourierShipment $shipment)
    {
        $this->resource = $shipment;
    }

    protected function columns(): array
    {
        return [
            ResourceListColumn::create('Consignment', function ($row) {
                return $row->consignment_number;
            }),
            ResourceListColumn::create('Carrier', function ($row) {
                return optional($row->courierService)->carrier;
            }),
            ResourceListColumn::create('Service Type', function ($row) {
                return optional($row->courierService)->service_type;
            }),
            ResourceListColumn::create('Service Code', function ($row) {
                return optional($row->courierService)->service_code;
            }),
            ResourceListColumn::create('Orders', function ($row) {
                return view('couriers::resource-lists.links', [
                    'links' => $row->orders->map(fn ($order) => [
                        'text' => "#{$order->reference}",
                        'route' => route('admin.orders.view', array_merge(request()->all(), ['order' => $order->id])),
                    ])->toArray(),
                    'count' => $row->orders->count()
                ])->render();
            })->setSearchAction(function ($query, $search) {
                $searchTerm = ltrim($search, '#');
                $query->whereHas('fulfillments', function ($query) use ($searchTerm) {
                    $query->whereHas('items', function ($query) use ($searchTerm) {
                        $query->whereHas('order', function ($query) use ($searchTerm) {
                            $query->where('reference', $searchTerm)->orWhere('id', $searchTerm);
                        });
                    });
                });
            }, 'Order Reference'),
            ResourceListColumn::create('Fulfillments', function ($row) {
                return view('couriers::resource-lists.links', [
                    'links' => $row->fulfillments->map(fn ($fulfillment) => [
                        'text' => "#{$fulfillment->reference}",
                        'route' => route('admin.orders.fulfillments.edit', array_merge(request()->all(), ['fulfillment' => $fulfillment])),
                    ])->toArray(),
                    'count' => $row->fulfillments->count()
                ])->render();
            })->setSearchAction(function ($query, $search) {
                $searchTerm = ltrim($search, '#');
                $query->whereHas('fulfillments', function ($query) use ($searchTerm) {
                    $query->where('reference', $searchTerm)->orWhere('id', $searchTerm);
                });
            }, 'Fulfillment Reference'),
            ResourceListColumn::create('Connector', function ($row) {
                return optional($row->courierConnector)->name;
            }),
            ResourceListColumn::create('Admin', function ($row) {
                if ($row->admin) {
                    return view('admin::resource-lists.link', [
                        'route' => route('admin.configuration.users.edit', array_merge(request()->all(), ['user' => $row->admin])),
                        'text' => $row->admin->name,
                    ])->render();
                }
            }),
            ResourceListColumn::create('Committed', function ($row) {
                return view('admin::resource-lists.status', [
                    'active' => $row->committed,
                    'pending' => !$row->committed,
                ])->render();
            }, 'status'),
            ResourceListColumn::create('Collected', function ($row) {
                return view('admin::resource-lists.status', [
                    'active' => optional($row->collection)->collected,
                    'pending' => !optional($row->collection)->collected,
                ])->render();
            }, 'status'),
        ];
    }

    protected function newQuery()
    {
        return $this->resource->newQuery();
        return $this->resource->newQuery()->whereHas('fulfillments')
            ->when(!request()->has('collected-status'), function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('collection', function ($query) {
                        $query->where('collected', false);
                    })->orWhereNull('courier_collection_id');
                });
            });
    }

    protected function sortBys(): array
    {
        return [
            ResourceListSortBy::create(null, function ($_, $query) {
                return $query->latest();
            }),
            ResourceListSortBy::create([
                'carrier-az' => 'Carrier A to Z',
                'carrier-za' => 'Carrier Z to A',
            ], function ($sortBy, $query) {
                return $query->join('courier_services', 'courier_services.id', '=', 'courier_shipments.courier_service_id')
                    ->orderBy('courier_services.carrier', $sortBy === 'carrier-az' ? 'asc' : 'desc');
            }),
            ResourceListSortBy::create([
                'connector-az' => 'Connector A to Z',
                'connector-za' => 'Connector Z to A',
            ], function ($sortBy, $query) {
                return $query->join('courier_connectors', 'courier_connectors.id', '=', 'courier_shipments.courier_connector_id')
                    ->orderBy('courier_connectors.name', $sortBy === 'connector-az' ? 'asc' : 'desc');
            }),
        ];
    }

    protected function handleSearch($search)
    {
        $this->query->whereLower('consignment_number', 'like', "%{$search}%");
    }

    public function backButtonLink()
    {
        if ($fulfillment = request()->query('fulfillment')) {
            return route('admin.orders.fulfillments.edit', $fulfillment);
        }

        return route('admin.modules');
    }

    public function title(): string
    {
        return 'Shipment Manager';
    }
}
