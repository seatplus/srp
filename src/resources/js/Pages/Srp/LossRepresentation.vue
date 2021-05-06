<template>
  <div class="space-y-4">
    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
      <div>
        <!--class="px-4 py-5 sm:p-6"-->
        <!-- Content goes here -->
        <SlotElement
          :item="container"
          @valueUpdate="addToSum"
        />
      </div>
    </div>
    <Slot
      v-for="slot in slots"
      :key="`${slot.name} ${location_id}`"
      :name="slot.name"
      :content="getLocationFlag(slot.flag)"
      @valueUpdate="addToSum"
    />
  </div>
</template>

<script>
import {ref, watch} from "vue";
import Slot from "./Slot";
import {useLoadCompleteResource} from "@/Functions/useLoadCompleteResource";
import SlotElement from "./SlotElement";

const slots = [
    {name: 'Highslots', flag: 'highslots'},
    {name: 'Midslots', flag: 'midslots'},
    {name: 'Lowslots', flag: 'lowslots'},
    {name: 'Subsystems', flag: 'subsystems'},
    {name: 'Fighter Tubes', flag: 'fighter_tubes'},
    {name: 'Fleet Hanga', flag: 'fleet_hangar'},
    {name: 'Ship Hangar', flag: 'ship_hangar'},
    {name: 'Specialized Hold', flag: 'specialized'},
    {name: 'Drone/Fighter Bay', flag: 'dronebay'},
    {name: 'Cargo', flag: 'everything_else'},
]

export default {
    name: "LossRepresentation",
    components: {SlotElement, Slot},
    props: {
        location_id: {
            required: true,
            type: Number,
        },
        container: {
            required: true,
            type: Object
        }
    },
    emits: ['valueUpdate'],
    setup(props, {emit}) {
        const sumValue = ref(0)
        const items = ref(useLoadCompleteResource('get.killmail.items', {location_id: props.location_id}));

        watch(sumValue, (newSubtotal, oldSubtotal) => {
            emit('valueUpdate', newSubtotal-oldSubtotal)
        })

        return {
            items: items.value.results,
            sumValue,
            slots
        }
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