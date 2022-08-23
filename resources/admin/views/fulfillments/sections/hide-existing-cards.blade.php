@push('scripts')
    <script>
        // Remove the existing process fulfillment box
        hideCard(document.getElementsByClassName('fulfillment-information')[0]);

        // Remove the existing tracking information
        hideCard(document.getElementsByClassName('tracking-form')[0]);

        // Remove existing shipping/fulfillment information
        hideCard(document.getElementsByClassName('shipping-information')[0]);
        hideCard(document.getElementsByClassName('fulfillment-information')[0]);

        function hideCard(e) {
            if (typeof e !== 'undefined') {
                e.classList.add('hidden');
            }
        }
    </script>
@endpush
