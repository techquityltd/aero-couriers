<template>
  <div class="flex">
    <div>
      <div class="w-full">
        <label class="block">{{ label }}</label>
        <input
          type="number"
          :id="label"
          :name="`configuration[${courier}][${label}][amount]`"
          v-model="numberOfParcels"
          class="w-full"
          :disabled="locked === 'true'"
          required
        />
      </div>
    </div>
    <div class="ml-2" v-for="index in Number(numberOfParcels)" :key="index">
      <label class="block">Parcel #<span v-html="index" /></label>
      <div class="price price--right w-24">
        <input
          type="number"
          autocomplete="off"
          min="0"
          class="w-full"
          v-model="parcelWeights[index]"
          :name="`configuration[${courier}][${label}][weights][${index}]`"
          :disabled="locked === 'true'"
          required
        />
        <label v-html="weightUnit" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    locked: {
      type: Boolean,
      default: false,
    },
    courier: String,
    label: String,
    weightUnit: String,
    parcels: Object,
    defaultWeight: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      numberOfParcels: this.parcels.amount,
      parcelWeights: this.parcels.weights,
    };
  },
  mounted() {
    for (var i = 1; i < 7; i++) {
      if (!this.parcelWeights[i]) {
        this.parcelWeights[i] = this.defaultWeight;
      }
    }
  },
  watch: {
    numberOfParcels(newParcels) {
      if (newParcels < 1) {
        this.numberOfParcels = 1;
      }
      if (newParcels > 6) {
        this.numberOfParcels = 6;
      }
    },
  },
};
</script>
