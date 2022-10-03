@extends('admin::layouts.list')

@section('content-above')
    <form action="{{ route('admin.courier-manager.shipments.bulk-collect', array_merge(request()->all())) }}" method="post">
        <fieldset class="w-full" @cannot('couriers.manage-services') disabled="disabled" @endcan>
            <div class="flex card mb-4">
                {{ csrf_field() }}
                @method('PUT')

                <div class="w-1/4 select">
                    <select id="carrier" name="carrier" class="select w-full text-base mt-0 mr-4 carrier-selector">
                        <option value="">Select Carrier...</option>
                        @foreach($carriers->keys() as $carrier)
                            <option value="{{ $carrier }}" @if(old('carrier')) selected @endif>{{ $carrier }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-1/5 flex items-end ml-2">
                    @can('couriers.manage-shipments')
                        <button id="confirm-collect" class="btn btn-secondary w-full text-center block align-bottom hidden" type="submit"></button>
                    @endcan
                </div>
            </div>
        </fieldset>
    </form>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const carrierSelector = document.getElementsByClassName("carrier-selector")[0];
            const collectButton = document.getElementById("confirm-collect");
            const availableDrivers = @json($carriers);

            carrierSelector.addEventListener("change", function(event) {
                if (availableDrivers[event.target.value]) {
                    collectButton.innerText = `Mark all ${event.target.value} as Collected`;
                    collectButton.classList.toggle("hidden", false)
                } else {
                    collectButton.classList.toggle("hidden", true)
                }
            });
        });
    </script>
@endpush
