<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <h3 class="text-lg leading-6 font-medium text-gray-900">
    Attackers
  </h3>
  <div
    v-if="hasFinishedLoading"
    class="space-y-4"
  >
    <div
      v-for="attacker in enhanced_attackers"
      class="flex flex-row justify-start items-center space-x-4"
    >
      <div class="">
        <EveImage
          :object="attacker"
          :size="256"
          tailwind_class="relative z-10 h-12 w-12 rounded-full"
        />
      </div>
      <div class="flex flex-col">
        <EveImage
          :object="attacker.ship"
          :size="256"
          tailwind_class="relative h-6 w-6 rounded-full"
        />

        <EveImage
          :object="attacker.weapon"
          :size="256"
          tailwind_class="relative h-6 w-6 rounded-full"
        />
      </div>
      <div class="flex flex-col">
        <span class="text-xs font-semibold tracking-wide uppercase">{{ attacker.name }}</span>
        <span class="text-sm text-gray-500">{{ attacker.corporation.name }} {{ attacker.alliance.name ? ` | ${attacker.alliance.name}` : '' }}</span>
      </div>
      <div v-if="attacker.final_blow" class="flex-shrink-0">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
          Final Blow
        </span>
      </div>
    </div>
  </div>
</template>


<script>
import axios from "axios";
import route from 'ziggy'
import {computed, onBeforeMount, ref} from "vue";
import EveImage from "@/Shared/EveImage";

export default {
    name: "Attackers",
    components: {
        EveImage
    },
    props: {
        attackers: {
            type: Array,
            required: true
        }
    },
    setup(props) {
        const hasFinishedLoading = ref(false)
        const ids_to_resolve = _.chain(props.attackers)
            .map((attacker) => [attacker.character_id, attacker.corporation_id, attacker.alliance_id ])
            .flatten()
            .uniq()
            .value()

        const resolved_ids = []

        const fetchNames = async () => {
            await axios.post(route('resolve.ids'), ids_to_resolve)
                .then(result => {
                    resolved_ids.push(...result.data)
                    hasFinishedLoading.value = true
                })
                .catch(error => console.log(error))
        }

        const findNameFor = (id) => {
            const object = _.find(resolved_ids, {id: id})

            return _.get(object, 'name')
        }

        const enhanced_attackers = computed(() => {
            return _.chain(props.attackers)
                .sortBy((o) => !o.final_blow)
                .map((attacker) => {

                    return {
                        character_id: attacker.character_id,
                        name: findNameFor(attacker.character_id),
                        corporation: {
                            corporation_id: attacker.corporation_id,
                            name: findNameFor(attacker.corporation_id)
                        },
                        alliance: {
                            alliance_id: attacker.alliance_id,
                            name: findNameFor(attacker.alliance_id)
                        },
                        ship: {
                            type_id: attacker.ship_type_id,
                        },
                        weapon: {
                            type_id: attacker.weapon_type_id,
                        },
                        final_blow: attacker.final_blow
                    }

                })
                .value()
        })

        onBeforeMount(() => fetchNames())

        return {
            enhanced_attackers,
            resolved_ids,
            hasFinishedLoading,
            findNameFor
        }
    }
}
</script>

<style scoped>

</style>