<?php

namespace Techquity\Aero\Couriers\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Techquity\Aero\Couriers\Http\Requests\CourierServiceRequest;
use Techquity\Aero\Couriers\Models\CourierService;
use Techquity\Aero\Couriers\ResourceLists\CourierServicesResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CourierServicesController extends Controller
{
    use UsesCourierDriver;

    public function index(CourierServicesResourceList $list, Request $request, ?CourierService $service = null)
    {
        return view('couriers::resource-lists.services', [
            'list' => $list = $list(),
            'results' => $list->apply($request->all())
                ->paginate($request->input('per_page', 24) ?? 24),
            'service' => $service
        ]);
    }

    public function update(CourierServiceRequest $request, CourierService $service)
    {
        $service->update($request->validated());

        return redirect()->route('admin.courier-manager.services.index')->with([
            'message' => __('Service :service was successfully updated.', [
                'service' => $service->name
            ]),
        ]);
    }

    public function store()
    {
        $this->getCourierDrivers()->each(function ($driver) {
            resolve($driver)->getServices()->each(function ($service) use ($driver) {
                $service['service_type'] = isset($service['service_type']) ? $service['service_type'] : $service['service_code'];
                $model = CourierService::query()
                    ->where('carrier', $driver::NAME)
                    ->where('service_type', $service['service_type'])
                    ->where('service_code', $service['service_code'])
                    ->first();

                if (!$model) {
                    $model = CourierService::create(Arr::add($service, 'carrier', $driver::NAME));
                } else {
                    $model->update(['description' => $service['description']]);
                }
            });
        });

        return redirect()->route('admin.courier-manager.services.index')->with([
            'message' => __('Services successfully refreshed'),
        ]);
    }
}
