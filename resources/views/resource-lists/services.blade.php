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
                        <input type="text" id="name" name="name" class="w-full" value="{{ old('name', $service->name) }}" required>
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
