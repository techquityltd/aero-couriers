<?php

namespace Techquity\Aero\Couriers\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Techquity\Aero\Couriers\Http\Requests\CourierPrinterRequest;
use Techquity\Aero\Couriers\Models\CourierPrinter;
use Techquity\Aero\Couriers\ResourceLists\CourierPrintersResourceList;

class CourierPrintersController extends Controller
{
    public function index(CourierPrintersResourceList $list, Request $request, ?CourierPrinter $printer = null)
    {
        return view('couriers::resource-lists.printers', [
            'printer' => $printer,
            'list' => $list = $list(),
            'results' => $list->apply($request->all())
                ->paginate($request->input('per_page', 24) ?? 24),
        ]);
    }

    public function store(CourierPrinterRequest $request)
    {
        CourierPrinter::create($request->validated());

        return redirect()->route('admin.courier-manager.printers.index')->with([
            'message' => __('A new printer was added'),
        ]);
    }

    public function update(CourierPrinterRequest $request, CourierPrinter $printer)
    {
        $printer->update($request->validated());

        return redirect()->route('admin.courier-manager.printers.index')->with([
            'message' => __('Printer was updated'),
        ]);
    }

    public function toggleAuto(CourierPrinter $printer)
    {
        $printer->update([
            'auto_print' => !$printer->auto_print
        ]);

        return back();
    }
}
