<?php

namespace Techquity\Aero\Couriers\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Aero\Fulfillment\Models\Fulfillment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Techquity\Aero\Couriers\Actions\CollectShipments;
use Techquity\Aero\Couriers\Actions\CommitShipments;
use Techquity\Aero\Couriers\Actions\DeleteFulfillment;
use Techquity\Aero\Couriers\Http\Requests\BulkCollectShipmentRequest;
use Techquity\Aero\Couriers\Models\CourierShipment;
use Techquity\Aero\Couriers\ResourceLists\CourierShipmentsResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CourierShipmentsController extends Controller
{
    use UsesCourierDriver;

    public function index(CourierShipmentsResourceList $list, Request $request)
    {
        return view('couriers::resource-lists.shipments', [
            'carriers' => $this->getCourierDrivers(),
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

    public function commit(Fulfillment $fulfillment)
    {
        if ((new CommitShipments())(collect([$fulfillment->courierShipment]))) {
            return redirect()->back()->with([
                'message' => __('Shipment successfully committed.'),
            ]);
        }

        return redirect()->back()->with([
            'error' => __('There was an issue committing shipment.'),
        ]);
    }

    public function collect(Fulfillment $fulfillment)
    {
        if ((new CollectShipments())(collect([$fulfillment->courierShipment]))) {
            return redirect()->back()->with([
                'message' => __('Shipment successfully marked as collected.'),
            ]);
        }

        return redirect()->back()->with([
            'error' => __('There was an issue marking the shipment as collected.'),
        ]);
    }

    public function bulkCollect(BulkCollectShipmentRequest $request)
    {
        $shipments = CourierShipment::query()
            ->whereDoesntHave('courierCollection')
            ->whereHas('courierConnector', function ($query) use ($request) {
                $query->where('carrier', $request->input('carrier'));
            })
            ->where('committed', true)
            ->cursor();

        if ((new CollectShipments())($shipments)) {
            return redirect()->back()->with([
                'message' => __('Shipments successfully marked as collected.'),
            ]);
        }

        return redirect()->back()->with([
            'error' => __('There was an issue marking the shipments as collected.'),
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
