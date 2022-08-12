<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>


    <!-- Be sure to use this with a layout container that is full-width on mobile -->
    <div class="bg-white overflow-hidden shadow sm:rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <form @submit.prevent="form.post(submitUrl)" class="space-y-8 divide-y divide-gray-200">
          <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
            <div>
              <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Request new SRP
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                  Copy and paste the link from the Character Sheet -> Interactions -> Combat Log -> Losses -> External URL into the box below.
                </p>
              </div>

              <div class="mt-6 sm:mt-5 space-y-6 sm:space-y-5">

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="killmail" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    External Url
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input v-model="form.killmailUrl" id="killmail" name="killmail_url" type="url" placeholder="https://esi.evetech.net/latest/killmails/92281357/19c919549fbadsasdwe4fc7938b21f2965f1baf0/" class="block max-w-lg w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md" />
                  </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                  <label for="about" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Ping
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <textarea v-model="form.description" id="about" name="about" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md" />
                    <p class="mt-2 text-sm text-gray-500">Put the ping content related to the fleet where you loose this ship.</p>
                  </div>
                </div>

              </div>
            </div>

          </div>

          <div class="pt-5">
            <div class="flex justify-end">
              <button type="submit" :disabled="form.processing" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Submit SRP
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <SubmittedRequests :requests="requests" />

  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import { useForm } from '@inertiajs/inertia-vue3'
import route from 'ziggy'
import SubmittedRequests from "./SubmittedRequests";

export default {
    name: "Request",
    components: {SubmittedRequests, PageHeader},
    props: {
        requests: {
            type: Object,
            required: true
        }
    },
    setup() {

        const form = useForm({
            killmailUrl: '',
            description: ''
        })

        const submitUrl = route('store.srp.request')

        return {
            form,
            submitUrl,
            pageTitle: 'SRP Request'
        }
    }
}
</script>

<style scoped>

</style>