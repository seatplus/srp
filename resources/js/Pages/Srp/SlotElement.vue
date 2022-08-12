<template>
  <li
    :class="[destroyed ? 'bg-red-50' : 'bg-white']"
    class="grid grid-cols-2 sm:grid-cols-8 sm:gap-x-0 sm:gap-y-1 grid-flow-row justify-items-auto text-sm text-gray-500 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500"
  >
    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-5">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Type
      </label>
      <div class="flex items-center">
        <div class="flex -space-x-1 relative z-0 overflow-hidden">
          <EveImage
            :object="type"
            :size="256"
            tailwind_class="relative z-10 h-5 w-5 rounded-full"
          />
        </div>
        <div class="ml-4">
          <h3 class="text-sm leading-6 font-medium text-gray-900">
            {{ type.name }}
          </h3>
        </div>
      </div>
    </div>

    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-1">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Quantity
      </label>
      {{ quantity }}
    </div>

    <div class="px-6 sm:px-3 py-4 sm:py-1 self-center whitespace-normal sm:col-span-2">
      <label class="block text-sm font-medium text-gray-700 sm:hidden">
        Price (ISK)
      </label>
      <div
        v-if="!editMode"
        class="flex justify-between"
      >
        <span>{{ localePrice }}</span>

        <button class="m1" @click="openInput">
          <PencilAltIcon
            class="h-5 h-5"
          />
        </button>

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
              v-model="price"
              type="number"
              name="price"
              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              :placeholder="localePrice"
            >
          </div>
          <button
            type="submit"
            :disabled="processing"
            class="m-1"
            @click="submit"
          >
            <CheckCircleIcon
              class="h-5 h-5 flex-shrink-0 text-green-600"
              @click="submit"
            />
          </button>
          <button
            type="reset"
            class="m-1"
          >
            <XCircleIcon
              class="h-5 h-5 flex-shrink-0 text-red-600"
              @click="closeInput"
            />
          </button>
        </div>
      </div>
    </div>
  </li>
</template>

<script>
import EveImage from "@/Shared/EveImage";
import { useGetPrice } from "@/Functions/useGetPrice";
import {computed, onBeforeMount, ref, watch} from "vue";
import { PencilAltIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/solid'
import { useForm } from '@inertiajs/inertia-vue3'
import axios from "axios";

export default {
    name: "SlotElement",
    components: {EveImage, PencilAltIcon, CheckCircleIcon, XCircleIcon},
    props: {
        item: {
            required: true,
            type: Object
        }
    },
    emits: ['valueUpdate'],
    setup(props, {emit}) {
        const marketPrice = _.get(useGetPrice(props.item.type_id), 'average_price', 0)
        const price = ref(0)
        const editMode = ref(false)
        const processing = ref(false)
        const oldPrice = ref(0)

        watch(price, (newPrice, oldPrice) => {
            emit('valueUpdate', props.item.quantity * (newPrice-oldPrice))
        })

        const localePrice = computed(() => {
            return price.value.toLocaleString()
        })

        onBeforeMount(() => {

            price.value = props.item.price > 0 ? props.item.price : marketPrice
        })

        return {
            editMode,
            price,
            localePrice,
            processing,
            oldPrice
        }
    },
    computed: {
        type() {
            return _.get(this.item, 'type')
        },
        destroyed() {
            return _.get(this.item, 'destroyed')
        },
        quantity() {
            return _.get(this.item, 'quantity')
        },
    },
    methods: {
        submit() {

            if(!this.processing) {
                this.processing = true

                axios.post(this.$route('post.killmail.item', this.item.id), {
                    price: this.price
                })
                    .then(() => this.editMode = false)
                    .catch(error => console.log(error))
            }

            this.processing = false
        },
        openInput() {
            this.oldPrice = this.price
            this.editMode = true
        },
        closeInput() {
            this.price = this.oldPrice
            this.editMode = false
        }
    }
}
</script>

<style scoped>

</style>