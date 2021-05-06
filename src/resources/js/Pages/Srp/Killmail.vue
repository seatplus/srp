<template>
  <teleport to="#head">
    <title>{{ title(pageTitle) }}</title>
  </teleport>

  <PageHeader>
    {{ pageTitle }}
    <div class="flex items-center">
      <div class="flex -space-x-1 relative z-0 overflow-hidden">
        <EveImage
          v-if="ship"
          :object="ship"
          :size="256"
          tailwind_class="relative z-10 h-12 w-12 rounded-full"
        />
      </div>
      <div class="ml-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          {{ ship.name }}
        </h3>
      </div>
      <div class="ml-4">
        <EntityBlock
          v-if="victim"
          :entity="victim"
        />
      </div>
    </div>
  </PageHeader>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="col-span-2">
      <LossRepresentation
        :location_id="killmail.killmail_id"
        :container="{destroyed: true, quantity: 1,type_id: ship.type_id, type: ship}"
        @valueUpdate="addToSum"
      />
    </div>
    <div>
      <div class="space-y-4">
        <HandleRequest
          :is-ready="isReady"
          :can-edit="canEdit"
          :step="step"
          :value="sumValue"
          :srp-request="srpRequest"
        />

        <Steps :current-step="step" />

        <div v-if="srpRequest.description">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Description
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            {{ srpRequest.description }}
          </p>
        </div>

        <div v-if="killmail.system">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            System
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            {{ ` ${killmail.system.name} (${killmail.system.security_status.toFixed(1)}) ${region}` }}
          </p>
        </div>

        <Attackers :attackers="killmail.attackers" />
      </div>
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import LossRepresentation from "./LossRepresentation";
import Steps from "./Steps";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import EveImage from "@/Shared/EveImage";
import {computed, onBeforeMount, ref, watch} from "vue";
import axios from "axios";
import route from 'ziggy'
import HandleRequest from "./HandleRequest";
import Attackers from "./Killmail/Attackers";

export default {
    name: "Killmail",
    components: {
        Attackers,
        HandleRequest,
        Steps,
        LossRepresentation,
        PageHeader, EntityBlock, EveImage},
    props: {
        killmail: {
            type: Object,
            required: true
        },
        srpRequest: {
            type: Object,
            required: true
        },
        step: {
            required: true,
            type: Number
        },
        canEdit: {
            required: true,
            type: Boolean
        }
    },
    setup(props) {
        const sumValue = ref(0)
        const ship = ref(_.get(props, 'killmail.ship'))
        const system = ref(_.get(props, 'killmail.system'))
        const victim = ref(null)
        const unknownIds = []
        const isReady = ref(props.step>1)

        if(!ship.value)
            unknownIds.push(_.get(props, 'killmail.ship_type_id'))

        if(!system.value)
            unknownIds.push(_.get(props, 'killmail.solar_system_id'))

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

        const markAsReady = _.debounce(function () {
            isReady.value = true
        }, 2500)

        watch(sumValue, () => markAsReady())

        onBeforeMount(() => {
            unknownIds.push(...[_.get(props, 'killmail.victim_character_id'), _.get(props, 'killmail.victim_corporation_id')])

            fetchNames();
            //markAsReady()

        })

        const region = computed(() => {
            const name = _.get(system.value, 'region.name')

            return name ? ` / ${name}` : ''
        })

        return {
            pageTitle: 'Killmail',
            ship,
            system,
            region,
            victim,
            sumValue,
            isReady
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