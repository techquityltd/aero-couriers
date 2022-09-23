@extends('admin::layouts.list')

@section('content-above')
    <form action="{{ $connector ? route('admin.courier-manager.connectors.update', $connector) : route('admin.courier-manager.connectors.store') }}" method="post">
        <fieldset class="w-full" @cannot('couriers.manage-connectors') disabled="disabled" @endcan>
            <div class="flex card mb-4">
                {{ csrf_field() }}
                @if($connector)
                    @method('PUT')
                @endif
                <div class="w-1/6">
                    <label class="block" for="carrier">Carrier</label>
                    <div class="w-full select">
                        <select id="carrier" name="carrier" class="select w-full text-base mt-0 mr-4 carrier-selector">
                            <option value="">Select Carrier...</option>
                            @foreach($carriers->keys() as $carrier)
                                <option value="{{ $carrier }}" @if(old('carrier', optional($connector)->carrier) === $carrier) selected @endif>{{ $carrier }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-1/6 ml-2">
                    <label class="block" for="name">Connector Name</label>
                    <input type="text" id="name" name="name" class="w-full" value="{{ old('name', optional($connector)->name) }}" required>
                </div>
                <div data-fields="url" class="w-1/6 flex-stretch hidden ml-2">
                    <label data-field="url" class="block" for="url">URL</label>
                    <input type="text" id="url" name="url" class="w-full" value="{{ old('url', optional($connector)->url) }}">
                </div>
                <div data-fields="user" class="w-1/6 ml-2 hidden">
                    <label data-field="user" class="block" for="user">User</label>
                    <input type="text" id="user" name="user" class="w-full" value="{{ old('user', optional($connector)->user) }}">
                </div>
                <div data-fields="password" class="w-1/6 ml-2 hidden">
                    <label data-field="password" class="block" for="password">Password</label>
                    <input type="password" id="password" name="password" class="w-full" value="{{ old('password', optional($connector)->password) }}">
                </div>
                <div data-fields="token" class="w-1/6 ml-2 hidden">
                    <label data-field="token" class="block" for="token">Token</label>
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

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const carrierSelector = document.getElementsByClassName("carrier-selector")[0]
            const availableFields = document.querySelectorAll("[data-fields]");
            const availableDrivers = @json($carriers);

            function resetAllFeeds()
            {
                availableFields.forEach(element => {
                    element.classList.add('hidden');
                });
            }

            carrierSelector.addEventListener("change", function(event) {
                resetAllFeeds();
                var selectedDriver = availableDrivers[event.target.value];
                if (selectedDriver !== undefined) {
                    availableFields.forEach(element => {
                        if (selectedDriver[element.children[0].dataset.field]) {
                            element.children[0].innerText = selectedDriver[element.children[0].dataset.field];
                            element.classList.remove('hidden');
                        }
                    });
                }
            });

            let selectedDriver = availableDrivers[carrierSelector.value];

            if (selectedDriver !== undefined) {
                availableFields.forEach(element => {
                    if (selectedDriver[element.children[0].dataset.field]) {
                        element.children[0].innerText = selectedDriver[element.children[0].dataset.field];
                        element.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
@endpush
