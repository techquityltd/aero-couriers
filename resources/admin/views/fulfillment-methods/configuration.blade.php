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

            <div class="w-1/2 px-2 mb-4">
                <label for="courier" class="block">Courier</label>

                <div class="select w-full">
                    <select id="courier" name="courier" class="w-full font-mono {{ $errors->has('courier') ? ' has-error' : '' }}">
                        <option></option>
                        @foreach ($couriers as $courier => $types)
                            <option value="{{ $courier }}" @if (old('courier', $fulfillmentMethod->courier ?? null) === $courier) selected @endif>{{ Str::title($courier) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @foreach ($couriers as $courier => $types)
                <div data-courier-options data-courier="{{ $courier }}" class="@if (old('courier', $fulfillmentMethod->courier ?? null) !== $courier) hidden @endif">
                    <div class="flex flex-wrap">
                        @include('courier::components.configuration-types', ['model' => $fulfillmentMethod ?? null, 'types' => $types, 'courier' => $courier])
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {

            const COURIER = document.getElementById('courier');
            const DRIVER = document.getElementById('driver');
            const courierOptions = document.getElementById('courier-options');

            function activateCourier(driver) {
                // Force driver to use the couriers driver
                DRIVER.value = 'courier';
                DRIVER.disabled = (driver !== '');

                // Enable the couriers setup form.
                document.querySelectorAll('[data-courier-options]').forEach(function(set) {
                    if (driver === set.dataset.courier) {
                        set.classList.remove('hidden');
                    } else {
                        DRIVER.value = "manual";
                        set.classList.add('hidden')
                    }
                });
            }

            COURIER.addEventListener('change', function(event) {
                activateCourier(event.target.value);
            });

            @if (array_key_exists(old('courier', $fulfillmentMethod->courier ?? ''), $couriers))
                activateCourier('{{ old('courier', $fulfillmentMethod->courier ?? '') }}');
            @endif

            // Beofore submitting we must re enable the driver input.
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    driver.disabled = false;
                });
            });
        });
    </script>
@endpush
