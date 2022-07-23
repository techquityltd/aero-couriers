
@include('courier::fulfillments.consignments')

<div id="courier-configuration-container"></div>
@isset($fulfillment->method->id)
    <input type="hidden" name="fulfillment_method" value="{{ $fulfillment->method->id }}" />
@endisset
@push('scripts')
    <script>
        function getConfiguration(driver) {
            fetch('{{ route('courier.fulfillment-config') }}/' + driver + '/{{ $fulfillment->id ?? '' }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            }).then(function(response) {
                return response.text();
            }).then(function(html) {
                document.getElementById('courier-configuration-container').innerHTML = html;
            }).finally(function() {
                @if ($fulfillment && $fulfillment->state === \Aero\Fulfillment\Models\Fulfillment::OPEN)
                    const container = document.getElementById('courier-configuration-container');
                    const settings = container.querySelectorAll('input, select, checkbox, textarea');

                    settings.forEach(function(item) {
                        item.disabled = true
                    });
                @endif
            })
        }

        window.addEventListener('DOMContentLoaded', () => {
            const method = document.getElementById('fulfillment-method');

            if (!method) {
                getConfiguration('{{ $fulfillment->method->id }}');
            } else {
                getConfiguration(method.value);
                method.addEventListener('change', function(event) {
                    getConfiguration(event.target.value)
                });
            }
        });
    </script>
@endpush
