{{-- @include('courier::fulfillments.consignments') --}}

@isset($fulfillment->method->id)
    <input type="hidden" name="fulfillment_method" value="{{ $fulfillment->method->id }}" />
@endisset
<div id="courier-configuration-container"></div>
@push('scripts')
    <script>
        function getConfiguration(method) {
            console.log('{{ route('courier.configuration.fulfillment') }}/' + method + '/{{ $fulfillment->id ?? '' }}');
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
                    @if ($fulfillment->state !== \Aero\Fulfillment\Models\Fulfillment::OPEN)
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
                    getConfiguration(event.target.value)
                });
            @endisset
        });
    </script>
@endpush
