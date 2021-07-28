<template>
  <TransitionRoot
    as="template"
    :show="open"
  >
    <Dialog
      as="div"
      static
      class="fixed inset-0 overflow-hidden"
      :open="open"
      @close="close()"
    >
      <div class="absolute inset-0 overflow-hidden">
        <DialogOverlay class="absolute inset-0" />

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex sm:pl-16">
          <TransitionChild
            as="template"
            enter="transform transition ease-in-out duration-500 sm:duration-700"
            enter-from="translate-x-full"
            enter-to="translate-x-0"
            leave="transform transition ease-in-out duration-500 sm:duration-700"
            leave-from="translate-x-0"
            leave-to="translate-x-full"
          >
            <div class="w-screen max-w-md">
              <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                <div class="px-4 py-6 sm:px-6">
                  <div class="flex items-start justify-between">
                    <h2
                      id="slide-over-heading"
                      class="text-lg font-medium text-gray-900"
                    >
                      Settings
                    </h2>
                    <div class="ml-3 h-7 flex items-center">
                      <button
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500"
                        @click="close()"
                      >
                        <span class="sr-only">Close panel</span>
                        <XIcon
                          class="h-6 w-6"
                          aria-hidden="true"
                        />
                      </button>
                    </div>
                  </div>
                </div>
                <!-- Main -->
                <div>
                  <div class="pb-1 sm:pb-6">
                    <div>
                      <!--                      <div class="relative h-40 sm:h-56">
                        <img
                          class="absolute h-full w-full object-cover"
                          src="https://images.unsplash.com/photo-1501031170107-cfd33f0cbdcc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&h=600&q=80"
                          alt=""
                        >
                      </div>-->
                      <div class="px-4 sm:flex sm:items-end sm:px-6">
                        <div class="sm:flex-1">
                          <div>
                            <div class="flex items-center">
                              <h3 class="font-bold text-xl text-gray-900 sm:text-2xl">
                                {{ notificationObject.title }}
                              </h3>
                            </div>
                            <p class="text-sm text-gray-500">
                              {{ notificationObject.description }}
                            </p>
                          </div>
                          <div class="mt-5 flex flex-wrap space-y-3 sm:space-y-0 sm:space-x-3">
                            <slot name="button" />
                          </div>
                          <TransitionRoot
                            as="div"
                            :show="notifiable && hasSaveButton"
                            class="mt-5 flex flex-wrap space-y-3 sm:space-y-0 sm:space-x-3"
                          >
                            <TransitionChild
                              as="template"
                              enter="transition-opacity duration-75"
                              enter-from="opacity-0"
                              enter-to="opacity-100"
                              leave="transition-opacity duration-150"
                              leave-from="opacity-100"
                              leave-to="opacity-0"
                            >
                              <button
                                :disabled="form.processing"
                                type="button"
                                class="flex-shrink-0 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:flex-1"
                                @click="subscribe"
                              >
                                Save
                              </button>
                            </TransitionChild>
                          </TransitionRoot>
                          <!--                          <div class="mt-5 flex flex-wrap space-y-3 sm:space-y-0 sm:space-x-3">
                            <button
                              type="button"
                              class="flex-shrink-0 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:flex-1"
                            >
                              Message
                            </button>
                            <button
                              type="button"
                              class="flex-1 w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                              Call
                            </button>
                            <span class="ml-3 inline-flex sm:ml-0">
                              <Menu
                                as="div"
                                class="relative inline-block text-left"
                              >
                                <MenuButton class="inline-flex items-center p-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-400 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                  <span class="sr-only">Open options menu</span>
                                  <DotsVerticalIcon
                                    class="h-5 w-5"
                                    aria-hidden="true"
                                  />
                                </MenuButton>
                                <transition
                                  enter-active-class="transition ease-out duration-100"
                                  enter-from-class="transform opacity-0 scale-95"
                                  enter-to-class="transform opacity-100 scale-100"
                                  leave-active-class="transition ease-in duration-75"
                                  leave-from-class="transform opacity-100 scale-100"
                                  leave-to-class="transform opacity-0 scale-95"
                                >
                                  <MenuItems class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                      <MenuItem v-slot="{ active }">
                                        <a
                                          href="#"
                                          :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']"
                                        >View profile</a>
                                      </MenuItem>
                                      <MenuItem v-slot="{ active }">
                                        <a
                                          href="#"
                                          :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm']"
                                        >Copy profile link</a>
                                      </MenuItem>
                                    </div>
                                  </MenuItems>
                                </transition>
                              </Menu>
                            </span>
                          </div>-->
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="px-4 pt-5 pb-5 sm:px-0 sm:pt-0">
                    <dl class="space-y-8 px-4 sm:px-6 sm:space-y-6">
                      <div>
                        <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:flex-shrink-0">
                          Instructions
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                          <p>
                            Below you will be able to select any affiliated character, corporation or alliance. By doing so, you will get the notifications of selected entity and its hierarchical subordinates.
                          </p>
                        </dd>
                      </div>
                      <div>
                        <dt class="text-sm font-medium text-gray-500 sm:w-56 sm:flex-shrink-0">
                          Characters:
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                          <fieldset>
                            <div class="mt-4 border-t border-b border-gray-200 divide-y divide-gray-200">
                              <div
                                v-for="(character_id, characterIdx) in characterIds"
                                :key="characterIdx"
                                class="relative flex items-start py-4"
                              >
                                <div class="min-w-0 flex-1 text-sm">
                                  <label
                                    :for="`character-${character_id}`"
                                    class="font-medium text-gray-700 select-none"
                                  >
                                    <EntityByIdBlock
                                      :id="character_id"
                                      :image-size="7"
                                      name-font-size="md"
                                    />
                                  </label>
                                </div>
                                <div class="ml-3 flex items-center h-5">
                                  <input
                                    :id="`character-${character_id}`"
                                    v-model="checkedCharacterIds"
                                    :name="`character-${character_id}`"
                                    :value="character_id"
                                    type="checkbox"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                  >
                                </div>
                              </div>
                            </div>
                          </fieldset>
                        </dd>
                      </div>
                      <div v-show="corporationIds.length > 0">
                        <dt class="text-sm font-medium text-gray-500 sm:w-56 sm:flex-shrink-0">
                          Corporations:
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                          <fieldset>
                            <div class="mt-4 border-t border-b border-gray-200 divide-y divide-gray-200">
                              <div
                                v-for="(corporation_id, corporationIdx) in corporationIds"
                                :key="corporationIdx"
                                class="relative flex items-start py-4"
                              >
                                <div class="min-w-0 flex-1 text-sm">
                                  <label
                                    :for="`corporation-${corporation_id}`"
                                    class="font-medium text-gray-700 select-none"
                                  >
                                    <EntityByIdBlock
                                      :id="corporation_id"
                                      :image-size="7"
                                      name-font-size="md"
                                    />
                                  </label>
                                </div>
                                <div class="ml-3 flex items-center h-5">
                                  <input
                                    :id="`corporation-${corporation_id}`"
                                    v-model="checkedCorporationIds"
                                    :name="`corporation-${corporation_id}`"
                                    :value="corporation_id"
                                    type="checkbox"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                  >
                                </div>
                              </div>
                            </div>
                          </fieldset>
                        </dd>
                      </div>
                      <div v-show="allianceIds.length > 0">
                        <dt class="text-sm font-medium text-gray-500 sm:w-56 sm:flex-shrink-0">
                          Alliances:
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                          <fieldset>
                            <div class="mt-4 border-t border-b border-gray-200 divide-y divide-gray-200">
                              <div
                                v-for="(alliance_id, allianceIdx) in allianceIds"
                                :key="allianceIdx"
                                class="relative flex items-start py-4"
                              >
                                <div class="min-w-0 flex-1 text-sm">
                                  <label
                                    :for="`alliance-${alliance_id}`"
                                    class="font-medium text-gray-700 select-none"
                                  >
                                    <EntityByIdBlock
                                      :id="alliance_id"
                                      :image-size="7"
                                      name-font-size="md"
                                    />
                                  </label>
                                </div>
                                <div class="ml-3 flex items-center h-5">
                                  <input
                                    :id="`alliance-${alliance_id}`"
                                    v-model="checkedAllianceIds"
                                    :name="`alliance-${alliance_id}`"
                                    :value="alliance_id"
                                    type="checkbox"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                  >
                                </div>
                              </div>
                            </div>
                          </fieldset>
                        </dd>
                      </div>
                      <!--                      <div>
                        <dt class="text-sm font-medium text-gray-500 sm:w-40 sm:flex-shrink-0">
                          Bio
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">
                          <p>
                            Enim feugiat ut ipsum, neque ut. Tristique mi id elementum praesent. Gravida in tempus feugiat netus enim aliquet a, quam scelerisque. Dictumst in convallis nec in bibendum aenean arcu.
                          </p>
                        </dd>
                      </div>-->
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import {
    Dialog,
    DialogOverlay,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import { XIcon } from '@heroicons/vue/outline'
import EntityByIdBlock from "@/Shared/Layout/Eve/EntityByIdBlock";
import {computed, ref, watch} from "vue";
import route from 'ziggy'
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    name: "NotificationSlideOver",
    components: {
        Dialog,
        DialogOverlay,
        EntityByIdBlock,
        TransitionChild,
        TransitionRoot,
        XIcon,
    },
    props: {
        open: {
            type: Boolean,
            default: () => false
        },
        notificationObject: {
            required: true,
            type: Object
        },
        notifiable: {
            required: false,
            type: Object,
            default: () => ({})
        },
    },
    emits: ['update:open'],
    setup(props, {emit}) {

        const characterIds = ref([])
        const corporationIds = ref([])
        const allianceIds = ref([])

        const checkedCharacterIds = ref([])
        const checkedCorporationIds = ref([])
        const checkedAllianceIds = ref([])

        const currentAffiliations = ref([])
        const form = useForm({
            notification: props.notificationObject.class,
            notifiable_id: _.get(props.notifiable, 'notifiable_id'),
            notifiable_type: _.get(props.notifiable, 'notifiable_type'),
        })

        const fetchAffiliatedEntities = async function (flavour) {

            await axios.post(route(`notification.affiliated.${flavour}s`), {
                notification: props.notificationObject.class
            }).then((result) => {
                switch (flavour) {
                    case 'character':
                        characterIds.value.push(...result.data)
                        break;
                    case 'corporation':
                        corporationIds.value.push(...result.data)
                        break;
                    case 'alliance':
                        allianceIds.value.push(...result.data)
                        break;
                }

            })
        }
        const fetchCurrentSubscribedEntities = async () => {
            await axios.post(route('notification.current.subscription'), {
                notification: props.notificationObject.class,
                notifiable_id: props.notifiable.notifiable_id,
                notifiable_type: props.notifiable.notifiable_type,
            }).then((result) => {
                currentAffiliations.value = result.data
                checkedCharacterIds.value = result.data.character_ids
                checkedCorporationIds.value = result.data.corporation_ids
                checkedAllianceIds.value = result.data.alliance_ids
            })
        }
        const close = function () {
            emit('update:open', false)
        }
        const subscribe = () => form.value
            .transform((data) => ({
                ...data,
                affiliated_entities: affiliatedEntities.value
            }))
            .post(route('notification.subscribe'), {
                onSuccess: () => currentAffiliations.value = affiliatedEntities.value
            })

        const affiliatedEntities = computed( function() {
            return {
                character_ids: checkedCharacterIds.value,
                corporation_ids: checkedCorporationIds.value,
                alliance_ids: checkedAllianceIds.value
            }
        })
        const hasSaveButton = computed(() => {

            if (_.isEmpty(currentAffiliations.value)) {
                return false
            }

            return !_.isEqual(currentAffiliations.value, affiliatedEntities.value)
        })

        watch(() => props.open, (open) => {
            if(open && _.isEmpty(characterIds.value)) {
                Promise.all([
                    fetchAffiliatedEntities('character'),
                    fetchAffiliatedEntities('corporation'),
                    fetchAffiliatedEntities('alliance')
                ]).catch(error => console.log(error))
            }

            if(open && _.isEmpty(currentAffiliations.value)) {
                fetchCurrentSubscribedEntities()
            }
        })


        return {
            close,
            checkedCharacterIds,
            characterIds,
            checkedCorporationIds,
            corporationIds,
            checkedAllianceIds,
            allianceIds,
            hasSaveButton,
            form,
            subscribe
        }
    },
}
</script>

