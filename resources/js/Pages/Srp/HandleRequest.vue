<template>
  <div class="space-y-4">
    <div
      v-if="!editMode"
      class="flex-none text-right text-lg text-gray-500"
    >
      <div class="flex justify-between">
        <span>{{ `Total ISK ${localeValue}` }}</span>
        <button
          v-if="canEdit && isReady"
          class="ml-1"
        >
          <PencilAltIcon
            class="h-5 w-5"
            @click="openEditMode"
          />
        </button>
      </div>
    </div>
    <div v-else>
      <div class="flex justify-between">
        <div>
          <label
            for="price"
            class="sr-only"
          >Price</label>
          <input
            id="price"
            v-model="form.sum"
            type="number"
            name="price"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
            :placeholder="form.sum.toLocaleString()"
          >
        </div>
        <div>
          <button
            type="submit"
            class="m-1"
            @click="saveInput"
          >
            <CheckCircleIcon class="h-7 w-7 flex-shrink-0 text-green-600" />
          </button>
          <button
            type="reset"
            class="m-1"
          >
            <XCircleIcon
              class="h-7 w-7 flex-shrink-0 text-red-600"
              @click="resetInput"
            />
          </button>
        </div>
      </div>
    </div>

    <button
      v-show="!isReady"
      type="button"
      disabled=""
      class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-not-allowed transition ease-in-out duration-150"
    >
      <svg
        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        />
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
      </svg>
      Calculating value
    </button>

    <inertia-link
      v-show="isReady && canEdit && step===1"
      as="button"
      :href="$route('submit.srp.request', srpRequest.id)"
      method="post"
      :data="{reimbursement: sum}"
      type="button"
      class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150"
    >
      <CheckCircleIcon class="-ml-1 mr-3 h-5 w-5 text-white" />
      Submit SRP request
    </inertia-link>

    <div
      v-show="step === 2 && canEdit"
      class="space-y-4"
    >
      <div class="sm:col-span-2">
        <label
          for="message"
          class="block text-sm font-medium text-gray-700"
        >
          Message
        </label>
        <div class="mt-1">
          <textarea
            id="message"
            v-model="form.message"
            name="message"
            rows="4"
            class="py-3 px-4 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
          />
        </div>
      </div>

      <span
              
        class="relative z-0 inline-flex shadow-sm rounded-md w-full"
      >
        <button
          type="button"
          :disabled="form.processing"
          class="relative justify-center inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 w-full"
          @click="handleRequest(true)"
        >
          <CheckCircleIcon class="h-6 w-6 mr-1 flex-shrink-0 text-green-600" />
          Approve
        </button>
        <button
          type="button"
          :disabled="form.processing"
          class="-ml-px relative justify-center inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 w-full"
          @click="handleRequest(false)"
        >
          <XCircleIcon class="h-6 w-6 mr-1 flex-shrink-0 text-red-600" />
          Deny
        </button>
      </span>
    </div>

    <div v-show="step === 3 && srpRequest.message">
      <dt class="text-xs font-semibold tracking-wide uppercase">
        Message
      </dt>
      <dd class="text-sm text-gray-500">
        {{ srpRequest.message }}
      </dd>
    </div>
  </div>
</template>

<script>
import {CheckCircleIcon, XCircleIcon, PencilAltIcon} from '@heroicons/vue/solid'
import {computed, ref } from "vue";
import {useForm} from "@inertiajs/inertia-vue3";
export default {
    name: "HandleRequest",
    components: {CheckCircleIcon, XCircleIcon, PencilAltIcon},
    props: {
        isReady: {
            type: Boolean,
            required: true
        },
        step: {
            type: Number,
            required: true
        },
        canEdit: {
            type: Boolean,
            required: true
        },
        value: {
            type: Number,
            required: true
        },
        srpRequest: {
            type: Object,
            required: true
        }
    },
    setup(props) {
        const editMode = ref(false)
        const reimbursement = ref(props.srpRequest.reimbursement)

        const form = useForm({
            sum: 0,
            message: '',
            killmail_hash: props.srpRequest.id
        })

        const sum = computed(() => {
            if(reimbursement.value > 0)
                return reimbursement.value

            return parseInt(form.sum) > 0 ? parseInt(form.sum) : props.value
        })

        const localeValue = computed(() => sum.value.toLocaleString())

        return {
            form,
            sum,
            localeValue,
            editMode,
            reimbursement
        }
    },
    methods: {

        openEditMode() {
            this.form.sum = this.sum

            this.editMode = true
        },
        saveInput() {
            this.reimbursement = this.form.sum
            this.editMode = false
        },
        resetInput() {
            this.form.sum = 0
            this.editMode = false
        },
        handleRequest(accepted) {
            this.form.transform((data) => ({
                ...data,
                sum: this.sum,
                decision: accepted
            }))
            .post(this.$route('handle.srp.request', this.srpRequest.id))
        }
    }

}
</script>

<style scoped>

</style>