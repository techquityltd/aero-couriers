@isset($fulfillment->method->id)
    <input type="hidden" name="fulfillment_method" value="{{ $fulfillment->method->id }}" />
@endisset

<div id="courier-configuration-container"></div>

@push('scripts')
    <script>
        const courierDrivers = @json(
            isset($fulfillment->method) ? [$fulfillment->method->id => $fulfillment->method->isCourier] :
            $methods->mapWithKeys(fn($method) => [$method->id => $method->isCourier])->toArray()
        );

        function getConfiguration(method) {
            fetch('{{ route('courier.configuration.fulfillment') }}/' + method + '/{{ $fulfillment->id ?? '' }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            }).then(function(response) {
                return response.text();
            }).then(function(html) {
                document.getElementById('courier-configuration-container').innerHTML = html;
            }).finally(function() {
                @isset($fulfillment)
                    @if ($fulfillment->state !== \Aero\Fulfillment\Models\Fulfillment::OPEN || $fulfillment->parent)
                        const container = document.getElementById('courier-configuration-container');
                        const settings = container.querySelectorAll('input, select, checkbox, textarea');

                        settings.forEach(function(item) {
                            item.disabled = true
                        });
                    @endif
                @endisset
            });
        }

        function shouldHideTrackingIfCourier(shouldHide)
        {
            let courierTracking = document.getElementById('courier-tracking');
            let trackingCode = document.getElementById('tracking-code');
            let container = trackingCode.parentElement.parentElement;

            if (shouldHide) {
                courierTracking.classList.remove('hidden')
                container.classList.add('hidden')
            } else {
                courierTracking.classList.add('hidden')
                container.classList.remove('hidden')
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const method = document.getElementById('fulfillment-method');

            @isset($fulfillment->method)
                getConfiguration('{{ $fulfillment->method->id }}');
                shouldHideTrackingIfCourier(courierDrivers['{{ $fulfillment->method->id }}']);
            @else
                getConfiguration(method.value);
                shouldHideTrackingIfCourier(method.value);
                method.addEventListener('change', function(event) {
                    getConfiguration(event.target.value);
                    shouldHideTrackingIfCourier(courierDrivers[event.target.value]);
                });
            @endisset
        });
    </script>
@endpush
