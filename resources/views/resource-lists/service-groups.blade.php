@extends('admin::layouts.list')

    @section('content-above')
        <form action="{{ isset($group) ? route('admin.courier-manager.service-groups.update', $group) : route('admin.courier-manager.service-groups.store')  }}" method="post">
            <fieldset class="w-full" @cannot('couriers.manage-services') disabled="disabled" @endcan>
                <div class="flex card mb-4">
                    {{ csrf_field() }}
                    @if($group)
                        @method('PUT')
                    @endif
                    <div class="w-1/5 mr-2">
                        <label class="block" for="name">Name</label>
                        <input type="text" id="name" name="name" class="w-full" value="{{ old('name', optional($group)->name) }}">
                    </div>
                    <div class="w-1/5 mr-2">
                        <label class="block" for="sort">Sort</label>
                        <input type="number" id="sort" name="sort" class="w-full" value="{{ old('sort', optional($group)->sort) }}">
                    </div>
                    <div class="w-1/5 flex items-end ml-2">
                        @can('couriers.manage-services')
                            <button class="btn btn-secondary w-full text-center block align-bottom" type="submit">
                                @if($group)
                                    Update Group
                                @else
                                    Add Group
                                @endif
                            </button>
                        @endcan
                    </div>
                </div>
            </fieldset>
        </form>
    @endsection

