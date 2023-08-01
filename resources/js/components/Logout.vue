<!-- Logout.vue -->

<template>
    <div class="tw-h-[97vh] tw-flex tw-items-center tw-justify-center">
        <p class="tw-text-lg tw-font-semibold tw-tracking-widest tw-text-gray-800 tw-uppercase dark:tw-text-gray-200">
            bye! hope to see you again :)
        </p>
    </div>>
</template>

<script>
export default {
    mounted() {
        this.logout();
    },
    methods: {
        logout() {
            setTimeout(() => {
                this.$authController.handleLogout();
            }, 2000);
        },
    },
};
</script>
