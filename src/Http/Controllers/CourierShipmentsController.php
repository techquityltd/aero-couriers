<?php

namespace Techquity\Aero\Couriers\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Techquity\Aero\Couriers\Actions\DeleteFulfillment;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;

class CourierShipmentsController extends Controller
{
    public function index(CourierShipmentsResourceList $list, Request $request)
    {
        return view('admin::resource-lists.index', [
            'list' => $list = $list(),
            'results' => $list->apply($request->all())
                ->paginate($request->input('per_page', 24) ?? 24),
        ]);
    }

    public function print(CourierShipment $shipment)
    {
        if (!$shipment->label || !Storage::has($shipment->label)) {
            return back()->with([
                'error' => __('Unable to find label for this shipment.'),
            ]);
        }

        return Storage::download($shipment->label);
    }

    public function delete(Fulfillment $fulfillment)
    {
        if ((new DeleteFulfillment())($fulfillment)) {
            return redirect()->route('admin.orders.view', $fulfillment->items()->first()->order)->with([
                'message' => __('Fulfillment successfully deleted.'),
            ]);
        }

        return redirect()->back()->with([
            'error' => __('There was an issue deleting shipment.'),
        ]);
    }

    public function request(CourierShipment $shipment)
    {
        return response($shipment->request, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    public function response(CourierShipment $shipment)
    {
        return response($shipment->response, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
