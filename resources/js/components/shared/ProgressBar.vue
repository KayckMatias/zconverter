<template>
    <div v-if="status == 'PENDING'" class="tw-mt-1 tw-w-[50%] tw-bg-yellow-600/20 tw-rounded-full dark:tw-bg-yellow-300/80">
        <div class="tw-transition-all tw-duration-300 tw-bg-yellow-600 tw-text-xs tw-font-medium tw-text-blue-100 tw-text-center tw-p-0.5 tw-leading-none tw-rounded-full"
            :style="`width: 100%`">PENDING</div>
    </div>

    <div v-else-if="status == 'DOWNLOADING' || status == 'CONVERTING'" class="tw-mt-1 tw-w-[50%] tw-bg-blue-600/20 tw-rounded-full dark:tw-bg-blue-300/80">
        <div class="tw-transition-all tw-duration-300 tw-bg-blue-600 tw-text-xs tw-font-medium tw-text-blue-100 tw-text-center tw-p-0.5 tw-leading-none tw-rounded-full"
        :style="`width: ${progress}%`"> {{ progress }}%</div>
    </div>

    <div v-else-if="status == 'COMPLETED'" class="tw-mt-1 tw-w-[50%] tw-bg-green-600/20 tw-rounded-full dark:tw-bg-green-300/80">
        <div class="tw-transition-all tw-duration-300 tw-bg-green-600 tw-text-xs tw-font-medium tw-text-blue-100 tw-text-center tw-p-0.5 tw-leading-none tw-rounded-full"
        :style="`width: ${progress}%`"> {{ progress }}%</div>
    </div>

    <div v-else class="tw-mt-1 tw-w-[50%] tw-bg-red-600/20 tw-rounded-full dark:tw-bg-red-300/80">
        <div class="tw-transition-all tw-duration-300 tw-bg-red-600 tw-text-xs tw-font-medium tw-text-blue-100 tw-text-center tw-p-0.5 tw-leading-none tw-rounded-full"
        :style="`width: 100%`">FAILED</div>
    </div>
</template>

<script>
export default {
    name: 'ProgressBar',
    props: {
        status: {
            type: String,
            default: 'PENDING'
        },
        progress: {
            type: Number,
            default: 100
        }
    }
};
</script>
