<?php

namespace Techquity\Aero\Couriers\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Techquity\Aero\Couriers\Actions\CollectShipments;
use Techquity\Aero\Couriers\Models\CourierCollection;
use Techquity\Aero\Couriers\ResourceLists\CourierCollectionsResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CourierCollectionsController extends Controller
{
    use UsesCourierDriver;

    public function index(CourierCollectionsResourceList $list, Request $request)
    {
        return view('admin::resource-lists.index', [
            'list' => $list = $list(),
            'results' => $list->apply($request->all())
                ->paginate($request->input('per_page', 24) ?? 24),
        ]);
    }

    public function manifest(CourierCollection $collection)
    {
        return Storage::download($collection->manifest);
    }
}
