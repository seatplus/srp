<template>
  <teleport to="#head">
    <title>{{ title(pageTitle) }}</title>
  </teleport>
  <div class="space-y-3">
    <PageHeader>
      {{ pageTitle }}
    </PageHeader>


    <!-- Be sure to use this with a layout container that is full-width on mobile -->
    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <!-- Content goes here -->
        <div class="space-y-8 divide-y divide-gray-200">
          <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
            <div>
              <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Profile
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                  This information will be displayed publicly so be careful what you share.
                </p>
              </div>

              <div class="mt-6 sm:mt-5 space-y-6 sm:space-y-5">
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5">
                  <label
                    for="photo"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Accountant
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <EntityBlock
                      :entity="receipt.accountant.main_character"
                      name-font-size="md"
                    />
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5">
                  <label
                    for="photo"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Receiver
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <EntityBlock
                      :entity="receipt.receiver.main_character"
                      name-font-size="md"
                    />
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label
                    for="about"
                    class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"
                  >
                    Amount
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <p class="mt-2 text-sm text-gray-500">
                      {{ receipt.amount.toLocaleString() }} ISK
                    </p>
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label
                    for="about"
                    class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2"
                  >
                    Payed at
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <p class="mt-2 text-sm text-gray-500">
                      <Time :timestamp="receipt.created_at" />
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <submitted-requests :requests="{data: receipt.srp_requests}" />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader.vue";
import EntityBlock from "@/Shared/Layout/Eve/EntityBlock.vue";
import Time from "@/Shared/Time.vue";
import SubmittedRequests from "../SubmittedRequests.vue";

export default {
    name: "Index",
    components: { PageHeader, EntityBlock, Time, SubmittedRequests},
    props: {
        receipt: {
            type: Object,
            required: true
        }
    },
    setup() {

        return {
            pageTitle: 'SRP Receipt',
        }
    }
}
</script>

<style scoped>

</style>