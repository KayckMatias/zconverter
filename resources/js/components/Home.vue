<template>
    <div class="tw-flex tw-justify-center tw-mt-28">
        <div>
            <div class="tw-flex tw-flex-col tw-items-center">
                <p
                    class="tw-mb-3 tw-p-2 tw-text-4xl tw-font-semibold tw-tracking-widest tw-text-primary-600 tw-uppercase focus:tw-outline-none focus:tw-shadow-outline">
                    zConverter
                </p>
                <p class="tw-text-lg tw-tracking-tight tw-text-gray-800 dark:tw-text-gray-200">Hi, <span
                        class="tw-font-semibold">{{ user.name }}</span>! What are
                    we going to convert today?</p>
            </div>

            <div class="tw-flex tw-justify-center">
                <div
                    class="tw-w-[90vw] tw-h-[60vh] tw-mt-8 tw-bg-white/70 tw-shadow-lg tw-shadow-gray-300 tw-rounded-xl dark:tw-bg-slate-800/70 dark:tw-shadow-gray-900 dark:tw-shadow-xl lg:tw-w-[50vw]">
                    <div class="tw-text-sm tw-font-medium tw-text-center tw-text-gray-500">
                        <ul
                            class="tw-pt-4 tw-flex tw-flex-wrap tw--mb-px tw-justify-center tw-text-gray-700 dark:tw-text-gray-200">
                            <li class="tw-mr-2 tw-cursor-pointer" v-for="(tab, index) in tabs" :key="index"
                                @click="changeTab(index)"
                                :class="{ 'tw-text-primary-600 dark:tw-text-primary-400': activeTab === index }">
                                <p class="tw-inline-block tw-p-4 tw-rounded-t-lg tw-border-b-2"
                                    :class="{ 'tw-border-primary-600': activeTab === index }">{{ tab.name }}</p>
                            </li>
                        </ul>
                    </div>
                    <component class="tw-mt-8" v-if="currentTab" :is="currentTab.component"></component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { markRaw } from 'vue';
import Convert from './pages/Convert.vue';
import Converting from './pages/Converting.vue';

export default {
    data() {
        return {
            user: [],
            tabs: [
                { name: 'New', component: markRaw(Convert) },
                { name: 'Queue', component: markRaw(Converting) },
            ],
            activeTab: 0,
            currentTab: [],
        }
    },

    async mounted() {
        this.user = await this.$authController.getAuthUser() ?? [];
        this.currentTab = this.tabs[this.activeTab];
    },

    methods: {
        changeTab(index) {
            this.activeTab = index;
            this.currentTab = this.tabs[index];
        },
    },
}
</script>
