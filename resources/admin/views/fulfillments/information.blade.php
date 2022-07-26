<div class="card w-full mt-4">
    <div class="w-full">
        <h3>Other Information</h3>
    </div>
    <div class="w-full">
        @if ($otherConsignments && $otherConsignments->count())
            <label class="block">Related Fulfillments</label>
            <ul class="mt-2 mb-4">
                @foreach ($otherConsignments as $consignment)
                    <li><a href="{{ route('admin.orders.fulfillments.edit', $consignment->id) }}">{{ $consignment->reference }}</a></li>
                @endforeach
            </ul>
        @endif

        @isset($shippingMethod)
            <label class="block">Shipping Method</label>
            <div class="mt-2 mb-4">
                {{ $shippingMethod->name }}
            </div>
        @endisset

        <div id="courier-tracking">
            <label class="block">Tracking Code</label>
            <div class="mt-2 mb-4">
                {{ $fulfillment->tracking_code ?? '-' }}
            </div>

            <label class="block">Tracking Url</label>
            <div class="mt-2 mb-4">
                {{ $fulfillment->tracking_url ?? '-' }}
            </div>
        </div>
    </div>
</div>

@isset($fulfillment)
    <div class="card w-full mb-4 mt-4">
        <div class="w-full">
            <h3>Manage Fulfillment</h3>
        </div>
        <div class="w-full flex">
            @if ($fulfillment->isErrored() || $fulfillment->isFailed())
                <form action="{{ route('admin.orders.fulfillments.process', $fulfillment) }}" method="post">
                    @csrf
                    <button class="btn btn-secondary mr-2" type="submit">Retry</button>
                </form>
            @endif

            @if ($fulfillment->isSuccessful())
                <form action="{{ route('admin.orders.fulfillments.process', $fulfillment) }}" method="post">
                    @csrf
                    <button class="btn btn-error mr-2" type="submit">Cancel</button>
                </form>
            @endif

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
