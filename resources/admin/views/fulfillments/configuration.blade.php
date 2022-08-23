{{-- Set the fulfillment method if using a parents --}}
@isset($parent)
    <input type="hidden" name="fulfillment_method" value="{{ $parent->fulfillment_method_id }}" />
    <input type="hidden" name="parent" value="{{ $parent->id }}" />
@endisset

{{-- Parent level container for configuration --}}
@if(!$parent)
    <div id="courier-configuration-container"></div>
@endif

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

        window.addEventListener('DOMContentLoaded', () => {
            const method = document.getElementById('fulfillment-method');

            @isset($fulfillment->method)
                getConfiguration('{{ $fulfillment->method->id }}');
            @else
                getConfiguration(method.value);
                method.addEventListener('change', function(event) {
                    getConfiguration(event.target.value);
                });
            @endisset
        });
    </script>
@endpush
