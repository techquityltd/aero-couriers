<div id="courier-configuration-container"></div>
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
            })
        }

        window.addEventListener('DOMContentLoaded', () => {

            const method = document.getElementById('fulfillment-method');

            getConfiguration(method.value)

            method.addEventListener('change', function(event) {
                getConfiguration(event.target.value)
            });
        });
    </script>
@endpush
