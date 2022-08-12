<template>
  <div class="space-y-3">
    <teleport to="#head">
      <title>{{ title(pageTitle) }}</title>
    </teleport>

    <PageHeader>
      {{ pageTitle }}
    </PageHeader>

    <div>
      <div class="sm:hidden">
        <label
          for="tabs"
          class="sr-only"
        >Select a tab</label>
        <select
          id="tabs"
          name="tabs"
          class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"
        >
          <option
            v-for="tab in tabs"
            :key="tab.name"
            :selected="tab.current"
            @click="$inertia.get(tab.href)"
          >
            {{ tab.name }}
          </option>
        </select>
      </div>
      <div class="hidden sm:block">
        <nav
          class="relative z-0 rounded-lg shadow flex divide-x divide-gray-200"
          aria-label="Tabs"
        >
          <Link
            v-for="(tab, tabIdx) in tabs"
            :key="tab.name"
            :href="tab.href"
            :class="[tab.current ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700', tabIdx === 0 ? 'rounded-l-lg' : '', tabIdx === tabs.length - 1 ? 'rounded-r-lg' : '', 'group relative min-w-0 flex-1 overflow-hidden bg-white py-4 px-4 text-sm font-medium text-center hover:bg-gray-50 focus:z-10']"
            :aria-current="tab.current ? 'page' : undefined"
          >
            <span>{{ tab.name }}</span>
            <span
              v-if="tab.count"
              :class="[tab.current ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900', 'hidden ml-3 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block']"
            >{{ tab.count }}</span>
            <span
              aria-hidden="true"
              :class="[tab.current ? 'bg-indigo-500' : 'bg-transparent', 'absolute inset-x-0 bottom-0 h-0.5']"
            />
          </Link>
        </nav>
      </div>
    </div>

    <slot />
  </div>
</template>

<script>
import PageHeader from "@/Shared/Layout/PageHeader";
import Layout from "@/Shared/SidebarLayout/Layout"
import route from 'ziggy'
import { Link } from "@inertiajs/inertia-vue3";

export default {
    name: "AdminTemplate",
    components: { PageHeader, Link},
    layout: (h, page) => h(Layout, { activeSidebarElement: route('review.srp.requests') }, [page]),
    props: {
        tabs: {
            type: Array,
            required: true
        }
    },
    setup() {
        return {
            pageTitle: 'Admin SRP Requests',
        }
    }
}
</script>

<style scoped>

</style>