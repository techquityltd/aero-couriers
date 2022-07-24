<div id="courier-configuration-container"></div>
@push('scripts')
    <script>
        function getConfiguration(courier) {
            fetch('{{ route('courier.configuration.fulfillment-method') }}/' + courier + '/{{ $fulfillmentMethod->id ?? '' }}', {
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

            const driver = document.getElementById('driver');

            getConfiguration(driver.value)

            driver.addEventListener('change', function(event) {
                getConfiguration(event.target.value)
            });
        });
    </script>
@endpush
