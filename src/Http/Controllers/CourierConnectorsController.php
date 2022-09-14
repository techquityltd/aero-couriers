<?php

namespace Techquity\Aero\Couriers\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Techquity\Aero\Couriers\Http\Requests\CourierConnectorRequest;
use Techquity\Aero\Couriers\Models\CourierConnector;
use Techquity\Aero\Couriers\Models\CourierPrinter;
use Techquity\Aero\Couriers\ResourceLists\CourierConnectorsResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CourierConnectorsController extends Controller
{
    use UsesCourierDriver;

    public function index(CourierConnectorsResourceList $list, Request $request, ?CourierConnector $connector = null)
    {
        return view('couriers::resource-lists.connectors', [
            'printers' => CourierPrinter::all(),
            'carriers' => $this->getCourierDrivers()->keys()->toArray(),
            'connector' => $connector,
            'list' => $list = $list(),
            'results' => $list->apply($request->all())
                ->paginate($request->input('per_page', 24) ?? 24),
        ]);
    }

    public function store(CourierConnectorRequest $request)
    {
        CourierConnector::create($request->validated());

        return redirect()->route('admin.courier-manager.connectors.index')->with([
            'message' => __('A new connector was created'),
        ]);
    }

    public function update(CourierConnectorRequest $request, CourierConnector $connector)
    {
        $connector->update($request->validated());

        return redirect()->route('admin.courier-manager.connectors.index')->with([
            'message' => __('Connector :connector was successfully updated.', [
                'connector' => $connector->name
            ]),
        ]);
    }

}
