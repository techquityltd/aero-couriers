<div class="card w-full mt-4">
    <div class="w-full">
        <h3>Courier Information</h3>
    </div>
    <div class="w-full">
        <label class="block">Shipping Method</label>
        <div class="mt-2 mb-4">
            {{ $fulfillment->shippingMethod->name }}
        </div>
    </div>

    <div class="w-full mt-4">
        <modal :hide-footer="true" :overflow-scroll="false">
            <template v-slot:header="modal">
                <div class="flex-1 flex items-center">
                    <h3 class="mb-0 pb-0 mr-2">Change Shipping Method</h3>
                </div>

                <button title="Close bulk manager" type="button" @click.prevent="modal.close">
                    <span class="block w-4 h-4">
                        @include('admin::icons.close')
                    </span>
                </button>
            </template>

            <template v-slot:body="modal">
                <div class="mt-4">
                    <p>Please select the shipping method:</p>
                </div>
            </template>

            <template v-slot:button="modal">
                <div>
                    <a href="" @click.prevent="modal.open" class="block fieldset-disabled-hide">
                        Change shipping method
                    </a>
                </div>
            </template>
        </modal>
    </div>
</div>
