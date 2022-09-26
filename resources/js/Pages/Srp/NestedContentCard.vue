<template>
  <div class="overflow-hidden shadow rounded-lg divide-y divide-gray-200">
    <div class="bg-white px-4 py-5 sm:px-6">
      <!---->
      <!-- Content goes here -->
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <EveImage
            :object="item.type"
            :size="256"
            tailwind_class="h-12 w-12 rounded-full"
          />
        </div>
        <div class="ml-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ item.type.name }}
          </h3>
        </div>
      </div>
      <div class="flex-none text-right text-sm text-gray-500">
        {{ `Subtotal ISK ${sumValue.toLocaleString()}` }}
      </div>
      <!-- We use less vertical padding on card headers on desktop than on body sections -->
    </div>
    <div>
      <!--class="px-4 py-5 sm:p-6"-->
      <!-- Content goes here -->
      <LossRepresentation
        :key="item.id"
        :location_id="item.id"
        :container="item"
        @valueUpdate="addToSum"
      />
    </div>
  </div>
</template>

<script>
import EveImage from "@/Shared/EveImage.vue";

export default {
    name: "NestedContentCard",
    components: {
        EveImage
    },
    props: {
        item: {
            required: true,
            type: Object
        }
    },
    emits: ['valueUpdate'],
    data() {
        return {
            sumValue: 0
        }
    },
    watch: {
        sumValue(newValue) {
            this.$emit('valueUpdate', newValue)
        }
    },
    beforeCreate: function () {
        //https://vuejs.org/v2/guide/components-edge-cases.html#Circular-References-Between-Components
        this.$options.components.LossRepresentation = require('./LossRepresentation.vue').default
    },
    methods: {
        getLocationFlag(flag) {
            return _.filter(this.items, {location_flag: flag})
        },
        addToSum(value) {
            this.sumValue += value
        }
    }
}
</script>

<style scoped>

</style>