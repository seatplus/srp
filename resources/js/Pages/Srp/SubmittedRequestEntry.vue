<template>
  <Link :href="route('view.srp.request', request.id)">
    <div class="bg-white grid grid-cols-2 sm:grid-cols-5 sm:gap-1 grid-flow-row hover:bg-gray-50 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
      <div class="px-6 py-4 sm:py-1 self-center truncate">
        <label class="block text-sm font-medium text-gray-700 sm:hidden">
          Ship
        </label>
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <EveImage
              :object="ship"
              :size="256"
              tailwind_class="h-5 w-5 rounded-full"
            />
          </div>
          <div class="ml-4">
            <h3
              v-if="ship"
              class="text-md leading-6 font-medium text-gray-900"
            >
              {{ ship.name }}
            </h3>
          </div>
        </div>
      </div>
      <div class="text-sm font-medium text-gray-500 px-6 py-1 self-center truncate">
        <label class="block text-sm font-medium text-gray-700 sm:hidden">
          System
        </label>
        <span v-if="system">{{ `${system.name} (${system.security_status.toFixed(1)}) ${region}` }}</span>
      </div>
      <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
        <label class="block text-sm font-medium text-gray-700 sm:hidden">
          Victim
        </label>
        <EntityBlock
          v-if="victim"
          :entity="victim"
          :image-size="5"
          name-font-size="sm"
        />
      </div>
      <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
        <label class="block text-sm font-medium text-gray-700 sm:hidden">
          Reimbursement
        </label>
        {{ `${request.reimbursement.toLocaleString()} ISK` }}
      </div>
      <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
        <label class="block text-sm font-medium text-gray-700 sm:hidden">
          Status
        </label>

        <div class="flex justify-between">
          <span>{{ request.status }}</span>

          <Link
            v-if="request.status === 'open'"
            :href="route('delete.srp.request', request.id)"
            as="button"
            method="delete"
            class="text-indigo-600 hover:text-indigo-700"
          >
            <XCircleIcon class="h-5 w-5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" />
          </Link>
        </div>
      </div>
    </div>
  </Link>
</template>

<script>
import EveImage from "@/Shared/EveImage.vue";
import {computed, onBeforeMount, ref} from "vue";
import axios from "axios";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import { XCircleIcon } from '@heroicons/vue/20/solid'
import { Link } from '@inertiajs/vue3'

export default {
    name: "SubmittedRequestEntry",
    components: {EveImage, EntityBlock, XCircleIcon, Link},
    props: {
        request: {
            type: Object,
            required: true
        }
    },
    setup(props) {
        const ship = ref(_.get(props, 'request.killmail.ship'))
        const system = ref(_.get(props, 'request.killmail.system'))
        const victim = ref(null)
        const unknownIds = []

        if(!ship.value)
            unknownIds.push(_.get(props, 'request.killmail.ship_type_id'))

        if(!system.value)
            unknownIds.push(_.get(props, 'request.killmail.solar_system_id'))

        const fetchNames = async () => {
            await axios.post(route('resolve.ids'), unknownIds)
                .then(result => {

                    let character = _.chain(result.data).find({category: "character"}).value()
                    let corporation = _.chain(result.data).find({category: "corporation"}).value()

                    victim.value = {
                        character_id: character.id,
                        name: character.name,
                        corporation: {
                            corporation_id: corporation.id,
                            name: corporation.name
                        }
                    }

                    let ship_helper = _.chain(result.data).find({category: "inventory_type"}).value()

                    if(ship_helper)
                        ship.value = {
                            type_id: ship_helper.id,
                            name: ship_helper.name,
                        }

                    let system_helper = _.chain(result.data).find({category: "solar_system"}).value()

                    if(system_helper)
                        system.value = {
                            security_status: 0,
                            name: system_helper.name,
                        }


                })
                .catch(error => console.log(error));
        }

        const region = computed(() => {
            const name = _.get(system.value, 'region.name')

            return name ? ` / ${name}` : ''
        })

        onBeforeMount(() => {
            unknownIds.push(...[_.get(props, 'request.killmail.victim_character_id'), _.get(props, 'request.killmail.victim_corporation_id')])

            fetchNames();

        })

        return {
            ship,
            system,
            region,
            victim
        }
    }
}
</script>

<style scoped>

</style>