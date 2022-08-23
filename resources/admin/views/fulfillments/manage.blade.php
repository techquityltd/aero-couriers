@isset($fulfillment)
    <div class="card w-full mb-4">
        <div class="w-full">
            <h3>Manage</h3>
        </div>
        <div class="w-full flex">
            {{-- Process Open or Pending fulfillments that are not child consignemnts --}}
            @if(($fulfillment->isOpen() || $fulfillment->isPending()) && !$fulfillment->parent)
                <form action="{{ route('admin.orders.fulfillments.process', $fulfillment) }}" method="post">
                    @csrf
                    <button class="btn btn-secondary mr-3" type="submit">Process</button>
                </form>
            @endif

            {{-- Retry Errored or Failed fulfillments that are not child consignments --}}
            @if (($fulfillment->isErrored() || $fulfillment->isFailed()) && !$fulfillment->parent)
                <form action="{{ route('admin.orders.fulfillments.process', $fulfillment) }}" method="post">
                    @csrf
                    <button class="btn btn-secondary mr-2" type="submit">Retry</button>
                </form>
            @endif

            {{-- Cancel a fulfillment --}}
            @if ($fulfillment->isSuccessful() && !$fulfillment->parent)
                <form action="{{ route('admin.orders.fulfillments.process', $fulfillment) }}" method="post">
                    @csrf
                    <button class="btn btn-error mr-2" type="submit">Cancel</button>
                </form>
            @endif

            {{-- Delete a fulfillment that is not pending or successful --}}
            @if (!$fulfillment->isSuccessful() && !$fulfillment->isPending())
                <form action="{{ route('admin.courier.fulfillment.delete', $fulfillment) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-error mr-2" type="submit">Delete</button>
                </form>
            @endif
        </div>
    </div>
@endisset

@push('scripts')
    <script>
        // Remove the existing process fulfillment box
        document.getElementsByClassName('process-fulfillment')[0].classList.add('hidden');
    </script>
@endpush
