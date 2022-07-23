<template>
    <div>
        <h3>Consignments</h3>
        <div>
            <div class="w-1/3">
                <label class="block">Parcels</label>
                <input type="number" :id="label" :name="`configuration[${courier}][${label}][amount]`"
                    v-model="numberOfParcels" class="w-full" :disabled="locked === 'true'" required />
            </div>
        </div>
        <div class="flex flex-wrap">
            <div class="w-1/3 my-4" v-for="index in Number(numberOfParcels)" :key="index">
                <div class="flex flex-col card m-2">
                    <h3>Parcel #<span v-html="index" /></h3>
                    <div>
                        <label class="block">Weight</label>
                        <div class="price price--right">
                            <input type="number" autocomplete="off" min="0" class="w-full"
                                v-model="weights[index]"
                                :name="`configuration[${courier}][${label}][weights][${index}]`"
                                :disabled="locked === 'true'" required />
                            <label v-html="weightUnit" />
                        </div>
                    </div>
                    <div v-if="withDimensions">
                        <div class="mt-2">
                            <label class="block">Length</label>
                            <div class="price price--right">
                                <input type="number" autocomplete="off" min="0" class="w-full"
                                    v-model="lengths[index]"
                                    :name="`configuration[${courier}][${label}][length][${index}]`"
                                    :disabled="locked === 'true'" required />
                                <label v-html="dimensionUnit" />
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="block">Width</label>
                            <div class="price price--right">
                                <input type="number" autocomplete="off" min="0" class="w-full"
                                    v-model="widths[index]"
                                    :name="`configuration[${courier}][${label}][weights][${index}]`"
                                    :disabled="locked === 'true'" required />
                                <label v-html="dimensionUnit" />
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="block">Height</label>
                            <div class="price price--right">
                                <input type="number" autocomplete="off" min="0" class="w-full"
                                    v-model="heights[index]"
                                    :name="`configuration[${courier}][${label}][weights][${index}]`"
                                    :disabled="locked === 'true'" required />
                                <label v-html="dimensionUnit" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        label: {
            type: String,
            required: true
        },
        parcels: {
            type: Object,
            required: true
        },
        locked: {
            type: Boolean,
            default: false,
        },
        courier: {
            type: String,
            required: true
        },
        withDimensions: {
            type: Boolean,
            default: false
        },
        defaultWeight: {
            type: Number,
            default: 1,
        },
        defaultLength: {
            type: Number,
            default: 1,
        },
        defaultWidth: {
            type: Number,
            default: 1,
        },
        defaultHeight: {
            type: Number,
            default: 1,
        },
        weightUnit: {
            type: String,
            default: 'g'
        },
        dimensionUnit: {
            type: String,
            default: 'cm'
        }
    },
    data() {
        return {
            numberOfParcels: this.parcels.amount,
            weights: [],
            lengths: [],
            widths: [],
            heights: []
        };
    },
    beforeMount() {
        for (var i = 1; i < 10; i++) {
            this.weights[i] = this.isset(this.parcels.weights, i) ? this.parcels.weights[i] : this.defaultWeight;
            this.lengths[i] = this.isset(this.parcels.lengths, i) ? this.parcels.lengths[i] : this.defaultLength;
            this.widths[i] = this.isset(this.parcels.widths, i) ? this.parcels.widths[i] : this.defaultWidth;
            this.heights[i] = this.isset(this.parcels.heights, i) ? this.parcels.heights[i] : this.defaultHeight;
        }
    },
    methods: {
        isset(data, key = null) {
            if (typeof data !== 'undefined') {
                if (key) {
                    if (typeof data[key] !== 'undefined') {
                        return true;
                    } else {
                        return false
                    }
                }
                return true;
            }
            return false;
        }
    },
    watch: {
        numberOfParcels(newParcels) {
            if (newParcels < 1) {
                this.numberOfParcels = 1;
            }
            if (newParcels > 9) {
                this.numberOfParcels = 9;
            }
        },
    },
};
</script>
