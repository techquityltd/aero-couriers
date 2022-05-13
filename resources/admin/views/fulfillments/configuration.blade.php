@if($methods->whereNotNull('fulfillmentConfiguration'))
    <input type="hidden" id="selectedCourier" name="courier" value="{{ old('courier', $methods->first()->courier) }}" />
    <div id="courier-configuration" class="card mb-4 w-full hidden [ aero-accordion ]" data-section="courier-options">
        <input type="checkbox" id="courierOptionsOpen" aria-hidden="true">

        <label class="aero-accordion-label" for="courierOptionsOpen" aria-hidden="true">
            <h3 class="m-0 p-0">Couriers</h3>
        </label>

        <div class="visually-hidden">
            <h3>Couriers</h3>
        </div>

        <div class="[ aero-accordion-content ]">

            <div class="pt-2">
                @foreach ($methods->whereNotNull('fulfillmentConfiguration') as $method)
                    <div data-courier-options data-method="{{ $method->id }}" data-courier="{{ $method->courier }}" class="card hidden">
                        <h3>{{ $method->courier }}</h3>
                        <div class="flex flex-wrap">
                            @include('courier::components.configuration-types', ['model' => $method ?? null, 'types' => $method->fulfillmentConfiguration->types(), 'courier' => $method->courier])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {

            const METHOD = document.getElementById('fulfillment-method');
            const COCONFIG = document.getElementById('courier-configuration');
            const COURIER = document.getElementById('selectedCourier');

            function resetCourierConfiguration()
            {
                COCONFIG.classList.add('hidden');
                COURIER.value = '';
                document.querySelectorAll('[data-courier-options]').forEach(function(set) {
                    set.classList.add('hidden')
                });
            }

            function activateCourier(method) {

                resetCourierConfiguration();

                document.querySelectorAll('[data-courier-options]').forEach(function(set) {
                    if (method === set.dataset.method) {
                        set.classList.remove('hidden');
                        COCONFIG.classList.remove('hidden');
                        COURIER.value = set.dataset.courier;
                    }
                });
            }

            if (!METHOD) {
                activateCourier("{{ $methods->first()->courier }}");
            } else {
                METHOD.addEventListener('change', function(event) {
                    activateCourier(event.target.value);
                });
            }

            activateCourier("{{ old('fulfillment_method', $methods->first())->id }}")
        });
    </script>
    @endpush
@endif
