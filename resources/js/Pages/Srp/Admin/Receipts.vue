<template>
  <AdminTemplate :tabs="tabs">
    <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
      <div class="bg-white px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Receipts
        </h3>
      </div>
      <div class="relative max-h-96 overflow-y-auto">
        <div class="hidden z-10 sticky top-0 border-b border-gray-200 bg-gray-50 text-sm font-medium text-gray-500 sm:grid sm:grid-cols-4 sm:gap-1 grid-flow-row ">
          <div class="px-6 py-1">
            Created at
          </div>
          <div class="px-6 py-1">
            Receiver
          </div>
          <div class="px-6 py-1">
            Amount
          </div>
          <div class="px-6 py-1">
            Accountant
          </div>
        </div>
        <ul class="divide-y divide-gray-200">
          <li
            v-for="receipt in requests.data"
            :key="receipt.id"
          >
            <Link :href="$route('view.srp.receipt', receipt.uuid)">
              <div class="bg-white grid grid-cols-2 sm:grid-cols-4 sm:gap-1 grid-flow-row hover:bg-gray-50 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500">
                <div class="px-6 py-4 sm:py-1 self-center truncate">
                  <label class="block text-sm font-medium text-gray-700 sm:hidden">
                    Created At
                  </label>
                  <Time :timestamp="receipt.created_at" />
                </div>
                <div class="text-sm font-medium text-gray-500 px-6 py-1 self-center truncate">
                  <label class="block text-sm font-medium text-gray-700 sm:hidden">
                    Receiver
                  </label>
                  <EntityBlock
                    v-if="receipt.receiver.main_character"
                    :entity="receipt.receiver.main_character"
                    :image-size="5"
                    name-font-size="sm"
                  />
                </div>
                <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
                  <label class="block text-sm font-medium text-gray-700 sm:hidden">
                    Amount
                  </label>
                  {{ receipt.amount.toLocaleString() }} ISK
                </div>
                <div class="text-sm font-medium text-gray-500 px-6 sm:py-1 sm:self-center">
                  <label class="block text-sm font-medium text-gray-700 sm:hidden">
                    Accountant
                  </label>
                  <EntityBlock
                    v-if="receipt.accountant.main_character"
                    :entity="receipt.accountant.main_character"
                    :image-size="5"
                    name-font-size="sm"
                  />
                </div>
              </div>
            </Link>
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
  </AdminTemplate>
</template>

<script>
import AdminTemplate from "@/Pages/Srp/Admin/AdminTemplate";
import { Link } from "@inertiajs/inertia-vue3";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock";
import Time from "@/Shared/Time";

export default {
    name: "Receipts",
    components: {AdminTemplate, EntityBlock, Time, Link},
    props: {
        requests: {
            type: Object,
            required: true
        },
        tabs: {
            type: Array,
            required: true
        }
    }
}
</script>

<style scoped>

</style>