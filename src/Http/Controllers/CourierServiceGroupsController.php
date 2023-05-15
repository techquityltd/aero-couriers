<?php

namespace Techquity\Aero\Couriers\Http\Controllers;

use Aero\Admin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Techquity\Aero\Couriers\Http\Requests\CourierServiceGroupRequest;
use Techquity\Aero\Couriers\Http\Requests\CourierServiceRequest;
use Techquity\Aero\Couriers\Models\CourierService;
use Techquity\Aero\Couriers\Models\CourierServiceGroup;
use Techquity\Aero\Couriers\ResourceLists\CourierServiceGroupsResourceList;
use Techquity\Aero\Couriers\ResourceLists\CourierServicesResourceList;
use Techquity\Aero\Couriers\Traits\UsesCourierDriver;

class CourierServiceGroupsController extends Controller
{
    use UsesCourierDriver;

    public function index(CourierServiceGroupsResourceList $list, Request $request, ?CourierServiceGroup $group = null)
    {
        return view('couriers::resource-lists.service-groups', [
            'list' => $list = $list(),
            'results' => $list->apply($request->all())
                ->paginate($request->input('per_page', 24) ?? 24),
            'group' => $group
        ]);
    }

    public function update(CourierServiceGroupRequest $request, CourierServiceGroup $group)
    {
        $group->update($request->validated());

        return redirect()->route('admin.courier-manager.service-groups.index')->with([
            'message' => __('Group :group was successfully updated.', [
                'group' => $group->name
            ]),
        ]);
    }

    public function store(CourierServiceGroupRequest $request)
    {
        CourierServiceGroup::create($request->validated());

        return redirect()->route('admin.courier-manager.service-groups.index')->with([
            'message' => __('A new courier service group was created'),
        ]);
    }
}
