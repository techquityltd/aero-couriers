@extends('admin::layouts.list')
@isset($service)
    @section('content-above')
        <form action="{{ route('admin.courier-manager.services.update', array_merge(request()->all(), ['service' => $service])) }}" method="post">
            <fieldset class="w-full" @cannot('couriers.manage-services') disabled="disabled" @endcan>
                <div class="flex card mb-4">
                    {{ csrf_field() }}
                    @method('PUT')
                    <div class="w-1/5 mr-2">
                        <label class="block" for="name">Display Name</label>
                        <input type="text" id="name" name="name" class="w-full" value="{{ old('name', $service->name) }}">
                    </div>
                    <div class="w-1/5 ml-2">
                        <label class="block" for="courier_service_group_id">Group</label>
                        <select name="courier_service_group_id" id="courier_service_group_id" class="w-full" style="margin-top: 0.5rem">
                            <option value=""></option>
                            @foreach(\Techquity\Aero\Couriers\Models\CourierServiceGroup::all() as $group)
                                <option value="{{ $group->id }}"
                                        {{ $group->id === $service->courier_service_group_id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-1/5 flex items-end ml-2">
                        @can('couriers.manage-services')
                            <button class="btn btn-secondary w-full text-center block align-bottom" type="submit">
                                Update Service
                            </button>
                        @endcan
                    </div>
                </div>
            </fieldset>
        </form>
    @endsection
@endisset
