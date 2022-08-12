<template>
  <teleport to="#head">
    <title>{{ title(pageTitle) }}</title>
  </teleport>

  <PageHeader>
    {{ pageTitle }}
  </PageHeader>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-1">
    <div class="col-span-2">
      <div class="border border-indigo-300 shadow rounded-md p-4 max-w-sm w-full mx-auto">
        <div class="animate-pulse flex space-x-4">
          <div class="rounded-full bg-indigo-400 h-12 w-12"></div>
          <div class="flex-1 space-y-4 py-1">
            <div class="h-4 bg-indigo-400 rounded w-3/4"></div>
            <div class="space-y-2">
              <div class="h-4 bg-indigo-400 rounded"></div>
              <div class="h-4 bg-indigo-400 rounded w-5/6"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <Steps :current-step="0" />
    </div>
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import {onBeforeMount, onUnmounted, ref, watch} from "vue";
import axios from "axios";
import route from 'ziggy'
import { Inertia } from '@inertiajs/inertia'
import Steps from "./Steps";

export default {
    name: "Processing",
    components: {Steps, PageHeader},
    props: {
        id: {
            required: true,
            type: String
        },
        batchId: {
            required: true,
            type: String
        }
    },
    setup(props) {

        const status = ref('')
        const batch_id = ref(props.batchId)
        const updateStatus = ref()

        function getStatus() {
            axios
                .get(route('get.batch_status', batch_id.value))
                .then(result => status.value = result.data.state)
        }

        onBeforeMount(async () => {
            if(batch_id.value) {
                await getStatus
                updateStatus.value = setInterval(getStatus,5000);
            }

        })

        watch(status, (newValue, oldValue) => {
            if(newValue === 'finished')
                Inertia.get(route('view.srp.request', props.id))
            if(oldValue === 'pending')
                clearInterval(updateStatus.value)


        })

        onUnmounted(() => {
            if (updateStatus.value)
                clearInterval(updateStatus.value)
        })

        return {
            pageTitle: 'Killmail - Processing',
            status,
            batch_id,
        }
    }
}
</script>

<style scoped>

</style>