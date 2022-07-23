<div class="card w-full mt-4">
    <div class="flex flex-wrap -mb-4 -mx-2">
        <div class="flex flex-wrap">
            <div class="w-full px-2 mb-4 mx-2">
                <split-consignment with-dimensions default-weight="" default-length="" deafult-width="" default-height=""
                    locked="{{ isset($fulfillment) && $fulfillment->state === 'open' ? true : false }}"
                    weight-unit="{{ setting('courier.default_weight', 'g') }}"
                    dimension-unit="{{ setting('courier.dimension_unit', 'cm') }}"
                    :parcels="{{ json_encode(old('consignments', isset($fulfillment) ? optional($fulfillment->courier_configuration)['consignments'] : [])) }}" />
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script src="{{ asset(mix('components.js', 'modules/techquity/couriers')) }}"></script>
    <script>
        window.AeroAdmin.vue.use(window.couriersComponents);
    </script>
@endpush
