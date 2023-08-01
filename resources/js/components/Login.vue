<template>
    <div class="tw-absolute tw-inset-0">
        <img class="tw-h-full tw-w-full tw-object-cover tw-brightness-[0.70]"
            src="https://images.unsplash.com/photo-1607112812619-182cb1c7bb61?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80">
        <div
            class="tw-absolute tw-inset-0 tw-backdrop-blur tw-bg-gradient-to-r tw-from-purple-800 tw-to-primary-700 tw-mix-blend-multiply">
        </div>
    </div>
    <div class="tw-relative">
        <div
            class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-px-6 tw-py-8 tw-mx-auto tw-h-screen lg:tw-py-0">
            <a href="#"
                class="tw-flex tw-items-center tw-mb-6 tw-text-2xl tw-font-semibold tw-tracking-widest tw-text-white tw-uppercase tw-rounded-lg focus:tw-outline-none focus:tw-shadow-outline">zConverter
            </a>

            <div class="tw-w-full tw-bg-white tw-rounded-lg tw-shadow-md md:tw-mt-0 sm:tw-max-w-md xl:tw-p-0">
                <div class="tw-p-6 tw-space-y-4 md:tw-space-y-6 sm:tw-p-8">
                    <h1 class="tw-text-xl tw-font-bold tw-leading-tight tw-tracking-tight tw-text-gray-900 md:tw-text-2xl">
                        Sign in to your account
                    </h1>
                    <form class="tw-space-y-4 md:tw-space-y-6" @submit.prevent="login">
                        <div>
                            <label for="email" class="tw-block tw-mb-2 tw-text-sm tw-font-medium tw-text-gray-900">Your
                                email</label>
                            <input type="text" v-model="form.email"
                                class="tw-bg-gray-50 tw-border tw-border-gray-300 tw-text-gray-900 sm:tw-text-sm tw-rounded-lg focus:tw-ring-primary-600 focus:border-primary-600 tw-block tw-w-full tw-p-2.5"
                                placeholder="name@example.com" required>
                        </div>
                        <div>
                            <label for="password"
                                class="tw-block tw-mb-2 tw-text-sm tw-font-medium tw-text-gray-900">Password</label>
                            <input type="password" v-model="form.password" placeholder="••••••••"
                                class="tw-bg-gray-50 tw-border tw-border-gray-300 tw-text-gray-900 sm:tw-text-sm tw-rounded-lg focus:tw-ring-primary-600 focus:border-primary-600 tw-block tw-w-full tw-p-2.5"
                                required="">
                        </div>
                        <button type="submit"
                            class="tw-w-full tw-rounded-lg tw-transition-all tw-duration-200 tw-bg-primary-600 tw-px-4 tw-py-1.5 tw-text-base tw-font-semibold tw-leading-7 tw-text-white hover:tw-text-white tw-shadow-sm tw-ring-1 tw-ring-primary-600 hover:tw-bg-primary-700">Sign
                            in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import router from "../router";

export default {
    data() {
        return {
            form: {
                email: '',
                password: ''
            }
        }
    },

    mounted() {
        if(this.$authController.isLoggedIn()){
            router.push('/')
        }
    },

    methods: {
        login(){
            this.$authController.handleLogin(this.form)
        }
    }
}
</script>
