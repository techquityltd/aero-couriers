<div class="card w-full mt-4">
    <div class="w-full">
        <h3>Other Consignements</h3>
    </div>
    <div class="w-full">
        <label class="block">Fulfillments</label>
        <searchable-select input-name="consignments" class="mt-2" url="{{ route('courier.consignments') }}" track-by="value" label="name" :multiple="true"></searchable-select>
    </div>

</div>
