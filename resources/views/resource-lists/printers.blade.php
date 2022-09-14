@extends('admin::layouts.list')

@section('content-above')
    <form action="{{ $printer ? route('admin.courier-manager.printers.update', $printer) : route('admin.courier-manager.printers.store') }}" method="post">
        <fieldset class="w-full" @cannot('couriers.manage-printers') disabled="disabled" @endcan>
            <div class="flex card mb-4">
                {{ csrf_field() }}
                @if($printer)
                    @method('PUT')
                @endif
                <div class="w-full flex">

                    <div class="">
                        <label class="block" for="name">Name</label>
                        <input type="text" id="name" name="name" class="w-full" value="{{ old('name', optional($printer)->name) }}" required>
                    </div>

                    <div class="ml-2">
                        <label class="block" for="host">Host</label>
                        <input type="text" id="host" name="host" class="w-full"
                            value="{{ old('host', optional($printer)->host) }}">
                    </div>

                    <div class="ml-2">
                        <label class="block" for="port">Port</label>
                        <input type="text" id="port" name="port" class="w-full"
                            value="{{ old('port', optional($printer)->port) }}">
                    </div>

                    <div class="w-1/7 flex items-end ml-2">
                        @can('couriers.manage-printers')
                            <button class="btn btn-secondary w-full text-center block align-bottom" type="submit">
                                @isset($printer) Update @else Add @endisset
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
@endsection
