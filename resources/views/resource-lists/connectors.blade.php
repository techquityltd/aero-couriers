@extends('admin::layouts.list')

@section('content-above')
    <form action="{{ $connector ? route('admin.courier-manager.connectors.update', $connector) : route('admin.courier-manager.connectors.store') }}" method="post">
        <fieldset class="w-full" @cannot('couriers.manage-connectors') disabled="disabled" @endcan>
            <div class="flex card mb-4">
                {{ csrf_field() }}
                @if($connector)
                    @method('PUT')
                @endif
                <div class="w-1/6 mr-2">
                    <label class="block" for="name">Name</label>
                    <input type="text" id="name" name="name" class="w-full" value="{{ old('name', optional($connector)->name) }}" required>
                </div>
                <div class="w-1/6 mr-2">
                    <label class="block" for="carrier">Carrier</label>
                    <div class="w-full select">
                        <select id="carrier" name="carrier" class="select w-full text-base mt-0 mr-4">
                            <option value="">Select Carrier...</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier }}" @if(old('carrier', optional($connector)->carrier) === $carrier) selected @endif>{{ $carrier }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-1/6 flex-stretch">
                    <label class="block" for="url">URL</label>
                    <input type="text" id="url" name="url" class="w-full" value="{{ old('url', optional($connector)->url) }}">
                </div>
                <div class="w-1/6 ml-2">
                    <label class="block" for="user">User</label>
                    <input type="text" id="user" name="user" class="w-full" value="{{ old('user', optional($connector)->user) }}">
                </div>
                <div class="w-1/6 ml-2">
                    <label class="block" for="password">Password</label>
                    <input type="password" id="password" name="password" class="w-full" value="{{ old('password', optional($connector)->password) }}">
                </div>
                <div class="w-1/6 ml-2">
                    <label class="block" for="token">Token</label>
                    <input type="password" id="token" name="token" class="w-full" value="{{ old('token', optional($connector)->token) }}">
                </div>
                <div class="w-1/6 flex items-end ml-2">
                    @can('couriers.manage-connectors')
                        <button class="btn btn-secondary w-full text-center block align-bottom" type="submit">
                            @if($connector)
                                Update Connector
                            @else
                                Add Connector
                            @endif
                        </button>
                    @endcan
                </div>
            </div>
        </fieldset>
    </form>
@endsection
