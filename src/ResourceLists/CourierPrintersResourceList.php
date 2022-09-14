<?php

namespace Techquity\Aero\Couriers\ResourceLists;

use Aero\Admin\ResourceLists\AbstractResourceList;
use Aero\Admin\ResourceLists\ResourceListColumn;
use Aero\Admin\Traits\IsExtendable;
use Illuminate\Routing\Route;
use Techquity\Aero\Couriers\Models\CourierPrinter;

class CourierPrintersResourceList extends AbstractResourceList
{
    use IsExtendable;

    protected $headerSlot = 'couriers.shipments.printers.header.buttons';

    protected $selected;

    public function __construct(CourierPrinter $printer)
    {
        $this->resource = $printer;
        $this->selected = optional(app(Route::class)->parameter('printer'))->id;
    }

    protected function columns(): array
    {
        return [
            ResourceListColumn::create('Name', function ($row) {
                return $row->name;
            }),
            ResourceListColumn::create('Host', function ($row) {
                return $row->host;
            }),
            ResourceListColumn::create('Port', function ($row) {
                return $row->port;
            }),
            ResourceListColumn::create('Auto Print', function ($row) {
                return view('couriers::resource-lists.toggle', [
                    'key' => 'auto-' . $row->id,
                    'active' => $row->auto_print,
                    'route' => route('admin.courier-manager.printers.toggle-auto', array_merge(request()->all(), ['printer' => $row])),
                ])->render();
            }),
            ResourceListColumn::create('', function ($row) {
                if ($row->id === $this->selected) {
                    return view('couriers::resource-lists.cancel-link', [
                        'route' => route('admin.courier-manager.printers.index', array_merge(request()->all())),
                    ])->render();
                } else {
                    return view('admin::resource-lists.manage-link', [
                        'route' => route('admin.courier-manager.printers.edit', array_merge(request()->all(), ['printer' => $row])),
                    ])->render();
                }
            }, 'action'),
        ];
    }

    public function backButtonLink()
    {
        return route('admin.courier-manager.shipments.index');
    }

    public function title(): string
    {
        return 'Courier Printers';
    }
}
