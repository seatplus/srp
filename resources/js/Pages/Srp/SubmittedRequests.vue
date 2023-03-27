<template>
  <!-- This example requires Tailwind CSS v2.0+ -->
  <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
    <div class="bg-white px-4 py-5 sm:px-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        <slot>
          Submitted Requests
        </slot>
      </h3>
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
          v-for="request in requests.data"
          :key="request.id"
        >
          <SubmittedRequestEntry :request="request" />
        </li>
      </ul>
    </div>
    <!--    <div class="px-4 py-5 sm:p-6">


    </div>-->
    <nav
      v-if="requests.from"
      class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
      aria-label="Pagination"
    >
      <div class="hidden sm:block">
        <p class="text-sm text-gray-700">
          Showing
          {{ ' ' }}
          <span class="font-medium">{{ requests.from }}</span>
          {{ ' ' }}
          to
          {{ ' ' }}
          <span class="font-medium">{{ requests.to }}</span>
          {{ ' ' }}
          of
          {{ ' ' }}
          <span class="font-medium">{{ requests.total }}</span>
          {{ ' ' }}
          results
        </p>
      </div>
      <div class="flex-1 flex justify-between sm:justify-end">
        <Link
          v-if="requests.prev_page_url"
          :href="requests.prev_page_url"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Previous
        </Link>
        <Link
          v-if="requests.next_page_url"
          :href="requests.next_page_url"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >
          Next
        </Link>
      </div>
    </nav>
  </div>
</template>

<script>
import SubmittedRequestEntry from "./SubmittedRequestEntry.vue";
import { Link } from '@inertiajs/vue3'

export default {
    name: "SubmittedRequests",
    components: {SubmittedRequestEntry, Link},
    props: {
        requests:  {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>

</style>