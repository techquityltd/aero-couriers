<div class="card w-full">
    <div class="w-full">
        <h3>Shipping Information</h3>
    </div>

    <div class="w-full -mb-4">
        <label class="block">Email</label>
        <div class="mt-2 mb-4">
            {{ $order->email }}
        </div>
        @if ($order->shippingAddress && $order->shippingAddress->mobile)
            <label class="block mb-4">Mobile Number</label>
            <div class="mt-2">
                {{ $order->shippingAddress->mobile }}
            </div>
        @endif
        @if ($order->shippingMethod)
            <label class="block">Shipping Method</label>
            <div class="mt-2 mb-4">{{ $order->shippingMethod->name }}</div>
        @endif
        @if ($order->shippingAddress)
            <label class="block">Shipping Address</label>
            <div class="mt-2 leading-normal mb-4">
                {{ $order->shippingAddress->full_name }}<br>
                {{ $order->shippingAddress->formatted_alt }}
            </div>
        @endif
        <label class="block" for="total-weight">Total Weight</label>
        <div class="price price--right mt-2 mb-4 w-24">
            <input type="number" id="total-weight" name="weight" value="{{ old('weight') }}"
                class="{{ $errors->has('weight') ? 'has-error' : '' }} w-full" autocomplete="off" min="0">
            <label>g</label>
        </div>
        @if(!$parent)
            <label class="block" for="delivery-note">Delivery Note</label>
            <textarea id="delivery-note" name="delivery_note"
                class="{{ $errors->has('delivery_note') ? 'has-error' : '' }} w-full mb-4">{{ old('delivery_note') }}
            </textarea>
        @endif
        @if ($sisters && $sisters->count())
            <label class="block">Related Fulfillments</label>
            <ul class="mt-2 mb-4">
                @foreach ($sisters as $consignment)
                    <li>
                        <a target="_blank" href="{{ route('admin.orders.fulfillments.edit', $consignment->id) }}">
                            {{ $consignment->reference }}
                        </a>
                        <small>
                            @if (optional($parent)->id === $consignment->id)
                                <a href="{{ route('admin.orders.fulfillments.new', ['order' => $order->id]) }}">(remove parent)</a>
                            @elseif(is_null($consignment->parent))
                                <a href="{{ route('admin.orders.fulfillments.new', ['order' => $order->id, 'parent' => $consignment->id]) }}">(make parent)</a>
                            @endif
                        </small>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@include('courier::fulfillments.sections.hide-existing-cards')

@isset($parent)
    @push('scripts')
        <script>
            // Remove the old fulfillment method selector
            document.getElementsByClassName('fulfillment-method')[0].remove();
        </script>
    @endpush
@endisset
