<div class="card mt-4 w-full [ aero-accordion ]" data-section="courier-options">
    <input type="checkbox" id="courierOptionsOpen" aria-hidden="true">

    <label class="aero-accordion-label" for="courierOptionsOpen" aria-hidden="true">
        <h3 class="m-0 p-0">Couriers</h3>
    </label>

    <div class="visually-hidden">
        <h3>Couriers</h3>
    </div>

    <div class="[ aero-accordion-content ]">
        <div class="pt-4">
            @foreach ($couriers as $courier => $types)
                <div data-courier-options data-courier="{{ $courier }}" class="@if (old('courier', $method->courier ?? null) !== $courier) hidden @endif">
                    <h2>{{ $courier }}</h2>
                    <div class="flex flex-wrap">
                        @include('courier::components.configuration-types', ['model' => $method ?? null, 'types' => $types, 'courier' => $courier])
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const methods = @json($fulfillmentMethods->pluck('courier', 'id')->filter()->toArray());

        function getSelectValues(select) {
            var result = [];
            var options = select && select.options;
            var opt;

            for (var i=0, iLen=options.length; i<iLen; i++) {
                opt = options[i];

                if (opt.selected) {
                result.push(opt.value || opt.text);
                }
            }

            return result;
        }

        function hideAllCouriers()
        {
            document.querySelectorAll('[data-courier-options]').forEach(element => {
                element.classList.add('hidden');
            });
        }

        function updateCourierForms(courier)
        {
            document.querySelectorAll(`[data-courier-options][data-courier=${methods[courier]}]`).forEach(element => {
                if (element !== 'undefined') {
                    element.classList.remove('hidden');
                }
            });
        }

        window.addEventListener('DOMContentLoaded', () => {

            const multiselect = document.getElementById('fulfillment-methods');

            getSelectValues(multiselect).forEach(element => {
                updateCourierForms(element);
            });

            multiselect.addEventListener('change', function(event) {
                // Hide all and show only selected.
                hideAllCouriers();

                getSelectValues(multiselect).forEach(element => {
                    updateCourierForms(element);
                });
            });
        });
    </script>
@endpush
