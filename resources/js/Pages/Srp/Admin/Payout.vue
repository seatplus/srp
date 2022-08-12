<template>
  <AdminTemplate :tabs="tabs">

    <div
      v-for="payment in payments"
      :key="payment.main_character.character_id"
      class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200"
    >
      <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
        <div class="-ml-4 -mt-2 flex items-center justify-between flex-wrap sm:flex-nowrap">
          <div class="ml-4 mt-2">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              <EntityBlock :entity="payment.main_character" />
            </h3>
          </div>
          <div class="ml-4 mt-2 flex-shrink-0 space-x-4">
            <span class="text-sm font-medium text-gray-500">{{payment.total.toLocaleString()}} ISK</span>
            <button
              v-if="!receipt(payment)"
              @click="submit(payment)"
              :disabled="isProcessing"
              type="button"
              class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Payout
            </button>
            <button
              v-if="receipt(payment)"
              @click="copyToClipboard(receipt(payment))"
              type="button"
              class="relative inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Copy receipt url to clipboard
              <ClipboardCopyIcon class="h-5 w-5 text-white" />
            </button>
          </div>
        </div>
      </div>
      <div class="relative max-h-96 overflow-y-auto">
        <div class="hidden z-10 sticky top-0 border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500 sm:grid sm:grid-cols-5 sm:gap-1 grid-flow-row ">
          <div class="px-6 py-1">
            Ship
          </div>
          <div class="px-6 py-1">
            System
          </div>
          <div class="px-6 py-1">
            Victim
          </div>
          <div class="px-6 py-1">
            Reimbursement
          </div>
          <div class="px-6 py-1">
            Status
          </div>
        </div>
        <ul class="divide-y divide-gray-200">
          <li
            v-for="request in payment.requests"
            :key="request.id"
          >
            <SubmittedRequestEntry :request="request" />
          </li>
        </ul>
      </div>
    </div>
  </AdminTemplate>
</template>

<script>
import AdminTemplate from "@/Pages/Srp/Admin/AdminTemplate";
import {useLoadCompleteResource} from "@/Functions/useLoadCompleteResource";
import {computed, ref} from "vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import SubmittedRequestEntry from "../SubmittedRequestEntry";
import axios from "axios";
import {ClipboardCopyIcon} from '@heroicons/vue/solid'

export default {
    name: "Payout",
    components: {SubmittedRequestEntry, AdminTemplate, EntityBlock, ClipboardCopyIcon},
    props: {
        tabs: {
            type: Array,
            required: true
        }
    },
    setup() {
        const data = ref(useLoadCompleteResource('payout.data.srp.requests'))
        const processed_payments = ref([])
        const isProcessing = ref(false)

        const payments = computed(() => _.chain(data.value.results)
                .groupBy('user_id')
                .map((user_requests) => {
                    return {
                        main_character: _.get(_.head(user_requests), 'user.main_character'),
                        user_id: _.get(_.head(user_requests), 'user.id'),
                        requests: user_requests,
                        total: _.sumBy(user_requests, 'reimbursement')
                    }
                }).value()
        )

        return {
            payments,
            processed_payments,
            isProcessing
        }
    },
    methods: {
        submit(payment) {
            const data = {
                amount: payment.total,
                receiving_user_id: payment.user_id,
                ids: _.map(payment.requests, (request) => request.id)
            }

            this.isProcessing = true

            axios.post(this.$route('process.payout.srp.requests'), data)
                .then((response) => this.processed_payments.push({
                    user_id: payment.user_id,
                    receipt_id: response.data
                }))
                .catch(error => console.log(error))

            this.isProcessing = false
        },
        receipt(payment) {
            const processed_payment =  _.find(this.processed_payments, {user_id: payment.user_id})

            return _.get(processed_payment, 'receipt_id')
        },
        async copyToClipboard(receipt_id) {

            const url = this.$route('view.srp.receipt', receipt_id)

            await window.navigator.clipboard.writeText(url);
            alert('Copied!');
        }
    }
}
</script>

<style scoped>

</style>