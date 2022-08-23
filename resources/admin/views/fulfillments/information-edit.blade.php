<div class="card w-full mb-4">
    <div class="w-full">
        <h3>Tracking Information</h3>
    </div>
    <div class="w-full">
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

<div class="card w-full">
    <div class="w-full">
        <h3>Fulfillment Information</h3>
    </div>
    <div class="w-full -mb-4">
        @if($parent)
            <label class="block">Consignment</label>
            <div class="mt-2 mb-4">
                This fulfillment is a consignment for {{ $parent->reference }}
            </div>
        @endif

        @if(isset($fulfillment->email) || isset($order->email))
            <label class="block">Email</label>
            <div class="mt-2 mb-4">
                {{ $fulfillment->email ?? $order->email}}
            </div>
        @endif

        @if(isset($fulfillment->mobile) || isset($order->mobile))
            <label class="block">Mobile Number</label>
            <div class="mt-2 mb-4">
                {{ $fulfillment->mobile }}
            </div>
        @endif

        @isset($fulfillment->method)
            <label class="block">Fulfillment Method</label>
            <div class="mt-2 mb-4">{{ $fulfillment->method->name }}</div>
        @endisset

        @isset($shippingMethod)
            <label class="block">Shipping Method</label>
            <div class="mt-2 mb-4">
                {{ $shippingMethod->name }}
            </div>
        @endisset

        @if(isset($fulfillment) || isset($order))
            <label class="block">Order Reference</label>
            <div class="mt-2 mb-4">
                <a href="{{ route('admin.orders.view', array_merge(request()->all(), ['order' => isset($fulfillment) ? $fulfillment->items->first()->order : $order])) }}">
                    {{ (isset($fulfillment) ? $fulfillment->items->first()->order : $order)->reference }}
                </a>
            </div>
        @endif
        @if(isset($fulfillment->address))
            <label class="block">Shipping Address</label>
            <div class="mt-2 leading-normal mb-4">
                {{ $fulfillment->address->full_name }}<br>
                {{ $fulfillment->address->formatted_alt }}
            </div>
        @endif
        <label class="block">Total Weight</label>
        <div class="mt-2 leading-normal mb-4">
            {{ number_format($fulfillment->weight, 2) }} g
        </div>

        @if ($sisters && $sisters->count())
        <label class="block">Related Fulfillments</label>
        <ul class="mt-2 mb-4">
            @foreach ($sisters as $consignment)
                <li><a href="{{ route('admin.orders.fulfillments.edit', $consignment->id) }}">{{ $consignment->reference }}</a></li>
            @endforeach
        </ul>
        @endif

        <label class="block">Delivery Note</label>
        <div class="mt-2 leading-normal mb-4">
            @if(! empty($fulfillment->delivery_note))
                {{ $fulfillment->delivery_note }}
            @else
                <span class="text-grey px-1">&mdash;</span>
            @endif
        </div>

    </div>
</div>

@include('courier::fulfillments.sections.hide-existing-cards')
