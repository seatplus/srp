
<template>
  <nav aria-label="Progress">
    <ol class="overflow-hidden">
      <li v-for="(step, stepIdx) in steps" :key="step.name" :class="[stepIdx !== steps.length - 1 ? 'pb-10' : '', 'relative']">
        <template v-if="step.status === 'complete'">
          <div v-if="(stepIdx !== steps.length - 1)" class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-indigo-600" aria-hidden="true" />
          <div class="relative flex items-start group">
            <span class="h-9 flex items-center">
              <span class="relative z-10 w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full group-hover:bg-indigo-800">
                <CheckIcon class="w-5 h-5 text-white" aria-hidden="true" />
              </span>
            </span>
            <span class="ml-4 min-w-0 flex flex-col">
              <span class="text-xs font-semibold tracking-wide uppercase">{{ step.name }}</span>
              <span class="text-sm text-gray-500">{{ step.description }}</span>
            </span>
          </div>
        </template>
        <template v-else-if="step.status === 'current'" condition="step.status === 'current'">
          <div v-if="(stepIdx !== steps.length - 1)" class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300" aria-hidden="true" />
          <div class="relative flex items-start group" aria-current="step">
            <span class="h-9 flex items-center" aria-hidden="true">
              <span class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-indigo-600 rounded-full">
                <span class="h-2.5 w-2.5 bg-indigo-600 rounded-full" />
              </span>
            </span>
            <span class="ml-4 min-w-0 flex flex-col">
              <span class="text-xs font-semibold tracking-wide uppercase text-indigo-600">{{ step.name }}</span>
              <span class="text-sm text-gray-500">{{ step.description }}</span>
            </span>
          </div>
        </template>
        <template v-else>
          <div v-if="(stepIdx !== steps.length - 1)" class="-ml-px absolute mt-0.5 top-4 left-4 w-0.5 h-full bg-gray-300" aria-hidden="true" />
          <div class="relative flex items-start group">
            <span class="h-9 flex items-center" aria-hidden="true">
              <span class="relative z-10 w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-full group-hover:border-gray-400">
                <span class="h-2.5 w-2.5 bg-transparent rounded-full group-hover:bg-gray-300" />
              </span>
            </span>
            <span class="ml-4 min-w-0 flex flex-col">
              <span class="text-xs font-semibold tracking-wide uppercase text-gray-500">{{ step.name }}</span>
              <span class="text-sm text-gray-500">{{ step.description }}</span>
            </span>
          </div>
        </template>
      </li>
    </ol>
  </nav>
</template>

<script>
import { CheckIcon } from '@heroicons/vue/solid'
import {computed} from "vue";

const stepsTemplate = [
    { name: 'Processing', description: 'Seatplus is fetching data from esi to represent your SRP request.'},
    { name: 'Open', description: 'Please review the killmail, check the item prices and the validity of the request, then submit the request so that a manager might reimburse your lost. '},
    { name: 'In Review', description: 'A Manager will soon be deciding whether this request is valid or invalid.'},
    { name: 'Processed', description: 'This request is considered to be processed.'},

]

export default {
    name: "Steps",
    components: {
        CheckIcon,
    },
    props: {
        currentStep: {
            type: Number,
            required: true
        }
    },
    setup(props) {

        const steps = computed(() => {
            return _.map(stepsTemplate, (step, index) => {
                step.status = 'upcoming'

                if(index < props.currentStep)
                    step.status = 'complete'

                if(index === props.currentStep)
                    step.status = 'current'

                return step
            })
        })

        return {
            steps,
        }
    },
}
</script>

<style scoped>

</style>