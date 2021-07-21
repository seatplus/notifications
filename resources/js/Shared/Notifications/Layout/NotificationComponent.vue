<template>
  <div
    class="group col-span-1 flex justify-center md:col-span-2 lg:col-span-1 space-x-4 cursor-pointer"
    @click="open = true"
  >
    <component
      :is="notificationObject.icon"
      :class="[open ? 'text-gray-800' : 'text-gray-400', 'h-12 group-hover:text-gray-800']"
    />
    <h3 :class="[open ? 'text-gray-800' : 'text-gray-400', 'group-hover:text-gray-800 font-medium leading-5 self-center']">
      {{ notificationObject.title }}
    </h3>

    <teleport to="#destination">
      <NotificationSlideOver
        v-model:open="open"
        :notifiable="notifiable"
        :notification-object="notificationObject"
      >
        <template #button>
          <slot v-if="!notifiable" />
        </template>
      </NotificationSlideOver>
    </teleport>
  </div>
</template>

<script>
import * as SolidHeroIcons from '@heroicons/vue/outline'
import NotificationSlideOver from "./NotificationSlideOver";
import {ref} from 'vue'

export default {
    name: "NotificationComponent",
    components: {
        NotificationSlideOver,
        ...SolidHeroIcons,
    },
    props: {
        notifiable: {
            required: false,
            type: Object,
            default: () => ({})
        },
        notificationObject: {
            required: true,
            type: Object
        }
    },
    setup() {
        const open = ref(false)

        return {
            open,
        }
    }
}
</script>

<style scoped>

</style>