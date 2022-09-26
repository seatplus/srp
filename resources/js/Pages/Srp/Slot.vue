<template>
  <CardWithHeader v-if="isShown">
    <template #header>
      <h3 class="text-lg leading-6 font-medium text-gray-900 cap">
        {{ name }}
      </h3>
      <div class="flex-none text-right text-sm text-gray-500">
        {{ `Subtotal ISK ${sumValue.toLocaleString()}` }}
      </div>
    </template>
    <ul class="divide-y divide-gray-200">
      <SlotElement
        v-for="(item, index) in contentWithoutNestedItems"
        :key="index"
        :item="item"
        @valueUpdate="addToSum"
      />
    </ul>

    <div
      v-for="(item, index) in contentWithNestedItems"
      :key="`nested ${index} ${item.item_type_id}`"
      class="bg-gray-200 p-4"
    >
      <nested-content-card
        :item="item"
        @valueUpdate="addToSum"
      />
    </div>
  </CardWithHeader>
</template>

<script>
import CardWithHeader from "@/Shared/Layout/Cards/CardWithHeader.vue";
import {computed, ref, watch} from 'vue'
import SlotElement from "./SlotElement.vue";
import NestedContentCard from "./NestedContentCard.vue";

export default {
    name: "Slot",
    components: {
        NestedContentCard,
        SlotElement, CardWithHeader
    },
    props: {
        name: {
            type: String,
            required: true
        },
        content: {
            type: Array,
            required: true
        }
    },
    emits: ['valueUpdate'],
    setup(props, {emit}) {

        const sumValue = ref(0)

        const isShown = computed(() => !_.isEmpty(props.content))
        const contentWithNestedItems = computed(() => _.filter(props.content, 'has_content'))
        const contentWithoutNestedItems = computed(() => _.filter(props.content, {has_content: false}))

        watch(sumValue, (newSubtotal, oldSubtotal) => {
            emit('valueUpdate', newSubtotal-oldSubtotal)
        })

        return {
            isShown,
            sumValue,
            contentWithNestedItems,
            contentWithoutNestedItems
        }
    },
    methods: {
        addToSum(value) {
            this.sumValue += value
        }
    }
}
</script>

<style scoped>

</style>