<div class="card w-full mt-4">
    <div class="w-full">
        <h3>Other Information</h3>
    </div>
    <div class="w-full">
        @if ($fulfillment->items()->first()->order->fulfillments->where('id', '!=', $fulfillment->id)->count())
            <label class="block">Related Fulfillments</label>
            <ul class="mt-2 mb-4">
                @foreach ($fulfillment->items()->first()->order->fulfillments->where('id', '!=', $fulfillment->id) as $other)
                    <li>{{ $other->reference }}</li>
                @endforeach
            </ul>
        @endif

        <label class="block">Shipping Method</label>
        <div class="mt-2 mb-4">
            {{ $fulfillment->items()->first()->order->shippingMethod->name }}
        </div>
    </div>
</div>

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
