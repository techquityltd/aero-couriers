<div class="card w-full mt-4">
    <div class="w-full">
        <h3>Consignements</h3>
    </div>
    @if ($fulfillment->parent)
        This fulfillment is a consignment for <a href="{{ route('admin.orders.fulfillments.edit', $fulfillment->parent->id) }}">{{ $fulfillment->parent->reference }}</a>
    @else
        <div class="w-full">
            <label class="block">Fulfillments</label>
            <searchable-select input-name="consignments" class="mt-2" url="{{ route('courier.consignments') }}" track-by="value" label="name" :multiple="true"></searchable-select>
        </div>
    @endif
   </div>
