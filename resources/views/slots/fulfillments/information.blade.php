<div class="manage-fulfillment card w-full mb-4">
    <div class="w-full">
        <h3>Manage Fulfillment</h3>
    </div>
    <div class="w-full flex">
        @if($shipment->consignment_number)
            <form class="w-1/3 mx-1" action="{{ route('admin.courier-manager.shipments.print', $shipment) }}" method="post">
                @csrf
                <button class="w-full btn btn-secondary" type="submit">Print Label</button>
            </form>
        @endif
        @if(!$shipment->committed)
            <form class="w-1/3 mx-1" action="{{ route('admin.courier-manager.shipments.commit', $fulfillment) }}" method="post">
                @csrf
                @method('PUT')
                <button class="w-full btn btn-secondary" type="submit">Commit</button>
            </form>
        @endif
        @if($shipment->committed && !$shipment->isComplete())
            <form class="w-1/3 mx-1" action="{{ route('admin.courier-manager.shipments.collect', $fulfillment) }}" method="post">
                @csrf
                @method('PUT')
                <button class="w-full btn btn-secondary" type="submit">Collected</button>
            </form>
        @endif
        @if(!$shipment->committed && !$shipment->isComplete())
            <form class="w-1/3 mx-1" action="{{ route('admin.courier-manager.shipments.delete', $fulfillment) }}" method="post">
                @csrf
                @method('DELETE')
                <button class="w-full btn btn-error" type="submit">Delete</button>
            </form>
        @endif
    </div>
</div>

<div class="courier-information card w-full mb-4">
    <div class="w-full">
        <h3>Shipment Information</h3>
    </div>
    <div class="w-full -mb-4">
        <label class="block">Status</label>
        <div class="mt-2 mb-4">
            <span class="bg-white rounded py-1 px-2 whitespace-no-wrap inline-block orb orb--2">
                <span class="inline-block w-orb h-orb rounded-full align-middle @if($shipment->isComplete()) bg-success @elseif($shipment->committed) bg-orange @elseif($shipment->failed) bg-error @else bg-grey @endif"></span>
                    <span class="pl-orb align-middle">@if($shipment->isComplete()) Collected @elseif($shipment->committed) Committed @elseif($shipment->failed) Failed @else Pending @endif</span>
            </span>
        </div>
        @if($shipment->isComplete())
            <label class="block">Collected On</label>
            <div class="mt-2 mb-4">
                {{ $shipment->courierCollection->created_at }}
            </div>
        @endif

        @if($shipment->failed_messages)
            <label class="block">Failed Reason</label>
            @foreach ($shipment->failed_messages as $message)
                <div class="mt-2 mb-4">
                    {{ $message }}
                </div>
            @endforeach
        @endif

        <label class="block">Consignment</label>
        <div class="mt-2 mb-4">
            @if($shipment->consignment_number)
                {{ $shipment->consignment_number }}
            @else
                <span class="text-grey px-1">&mdash;</span>
            @endif
        </div>
        <label class="block">Responses</label>
        <div class="mt-2 mb-4">
            <a href="{{ route('admin.courier-manager.shipments.request', $shipment) }}" target="_blank">request</a> / <a href="{{ route('admin.courier-manager.shipments.response', $shipment) }}" target="_blank">response</a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Remove the existing process fulfillment box
        hideCard(document.getElementsByClassName('process-fulfillment')[0]);

        function hideCard(e) {
            if (typeof e !== 'undefined') {
                e.classList.add('hidden');
            }
        }
    </script>
@endpush
