<template>
    <div>
        <div class="tw-flex tw-flex-col tw-items-center tw-justify-center">
            <p class="tw-text-lg tw-font-semibold tw-tracking-widest tw-text-gray-800 tw-uppercase dark:tw-text-gray-200">
                CONVERTING FILES</p>

            <div class="tw-w-[92%] tw-mt-5 lg:tw-w-[90%]">
                <div v-if="loadingFiles" class="tw-flex tw-justify-center tw-justify-items-center">
                    <Loading class="tw-text-primary-600" :size="8" />
                </div>

                <ul v-else-if="convert_files.length > 0" role="list" class="tw-space-y-2 tw-overflow-y-auto tw-h-80">
                    <li v-for="(convert, index) in convert_files" :key="index"
                        class="tw-py-3 tw-bg-slate-100 tw-p-4 tw-rounded-lg sm:tw-py-4 lg:tw-mr-2 dark:tw-bg-gray-900">
                        <div class="tw-flex tw-items-center tw-space-x-4">
                            <div class="tw-flex-shrink-0">
                                <StatusIcon :status="convert.status" size="8" />
                            </div>
                            <div class="tw-flex-1 tw-min-w-0">
                                <div class="tw-flex">
                                    <p
                                        class="tw-max-w-[55%] tw-text-sm tw-font-medium tw-text-gray-900 tw-truncate dark:tw-text-gray-200">
                                        [.{{ convert.to_extension }}] {{ convert.original_name }}
                                    </p>
                                </div>
                                <ProgressBar :status="convert.status" :progress="convert.progress" />
                                <div class="tw-flex">
                                    <p
                                        class="tw-max-w-[55%] tw-text-xs tw-font-medium tw-text-gray-900 tw-truncate dark:tw-text-gray-200">
                                        Custom Options: {{ this.formatOptions(convert.options) }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="convert.status == 'COMPLETED'">
                                <button type="button" @click="downloadConverted(convert.id, convert.file_id)"
                                    class="tw-transition-all tw-duration-300 tw-p-1.5 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-primary-600 tw-rounded-lg tw-border tw-border-primary-600 hover:tw-bg-primary-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-primary-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-primary-600 dark:hover:tw-bg-primary-700 dark:focus:tw-ring-primary-600">
                                    <svg class="tw-w-6 tw-h-6" viewBox="0 0 24 24" fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="m12 16 4-5h-3V4h-2v7H8z"></path>
                                        <path d="M20 18H4v-7H2v7c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2v-7h-2v7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div v-if="convert.status == 'FAILED'">
                                <button type="button" @click="retryConvert(convert.id)"
                                    class="tw-transition-all tw-duration-300 tw-p-1.5 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-primary-600 tw-rounded-lg tw-border tw-border-yellow-600 hover:tw-bg-yellow-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-yellow-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-yellow-600 dark:hover:tw-bg-yellow-700 dark:focus:tw-ring-yellow-600">
                                    <svg class="tw-w-4 tw-h-4 tw-m-1" viewBox="0 0 16 16"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill="currentColor"
                                            d="M14.9547098,7.98576084 L15.0711,7.99552 C15.6179,8.07328 15.9981,8.57957 15.9204,9.12636 C15.6826,10.7983 14.9218,12.3522 13.747,13.5654 C12.5721,14.7785 11.0435,15.5888 9.37999,15.8801 C7.7165,16.1714 6.00349,15.9288 4.48631,15.187 C3.77335,14.8385 3.12082,14.3881 2.5472,13.8537 L1.70711,14.6938 C1.07714,15.3238 3.55271368e-15,14.8776 3.55271368e-15,13.9867 L3.55271368e-15,9.99998 L3.98673,9.99998 C4.87763,9.99998 5.3238,11.0771 4.69383,11.7071 L3.9626,12.4383 C4.38006,12.8181 4.85153,13.1394 5.36475,13.3903 C6.50264,13.9466 7.78739,14.1285 9.03501,13.9101 C10.2826,13.6916 11.4291,13.0839 12.3102,12.174 C13.1914,11.2641 13.762,10.0988 13.9403,8.84476 C14.0181,8.29798 14.5244,7.91776 15.0711,7.99552 L14.9547098,7.98576084 Z M11.5137,0.812976 C12.2279,1.16215 12.8814,1.61349 13.4558,2.14905 L14.2929,1.31193 C14.9229,0.681961 16,1.12813 16,2.01904 L16,6.00001 L12.019,6.00001 C11.1281,6.00001 10.6819,4.92287 11.3119,4.29291 L12.0404,3.56441 C11.6222,3.18346 11.1497,2.86125 10.6353,2.60973 C9.49736,2.05342 8.21261,1.87146 6.96499,2.08994 C5.71737,2.30841 4.57089,2.91611 3.68976,3.82599 C2.80862,4.73586 2.23802,5.90125 2.05969,7.15524 C1.98193,7.70202 1.47564,8.08224 0.928858,8.00448 C0.382075,7.92672 0.00185585,7.42043 0.0796146,6.87364 C0.31739,5.20166 1.07818,3.64782 2.25303,2.43465 C3.42788,1.22148 4.95652,0.411217 6.62001,0.119916 C8.2835,-0.171384 9.99651,0.0712178 11.5137,0.812976 Z" />
                                    </svg>
                                </button>
                            </div>
                            <div v-if="convert.status == 'COMPLETED' || convert.status == 'FAILED'">
                                <button type="button" @click="deleteConverted(convert.id)"
                                    class="tw-transition-all tw-duration-300 tw-p-1.5 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-red-600 tw-rounded-lg tw-border tw-border-red-600 hover:tw-bg-red-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-red-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-red-600 dark:hover:tw-bg-red-700 dark:focus:tw-ring-red-600">
                                    <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
                <div v-else>
                    <p class="tw-text-center tw-font-medium tw-text-gray-800 dark:tw-text-gray-200">
                        Nothing found :( Convert a new file!
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { toast } from "vue3-toastify";
import Loading from "../shared/Loading.vue"
import StatusIcon from "../shared/StatusIcon.vue"
import ProgressBar from "../shared/ProgressBar.vue"

export default {
    components: {
        Loading,
        StatusIcon,
        ProgressBar
    },
    data() {
        return {
            loadingFiles: false,
            convert_files: [],
        }
    },

    async mounted() {
        this.getConvertFiles();

        const user = await this.$authController.getAuthUser();

        Echo.private(`Converter.Progress.${user.id}`)
            .listen('.Converter.Progress', (e) => {
                this.updateProgress(e);
            });
    },

    methods: {
        updateProgress(item) {
            const index = this.convert_files.findIndex(file => file.id === item.id)
            if (index !== -1) {
                const updatedItem = {
                    ...this.convert_files[index],
                    status: item.status,
                    progress: item.progress,
                    last_update: item.last_update,
                };
                this.convert_files[index] = updatedItem;
            }
        },
        downloadConverted(id, file_id) {
            const downloadUrl = `/api/file/download/${file_id}/conversion/${id}/generate`;
            const checkStatus = toast.loading("Please Wait...", {
                position: toast.POSITION.TOP_RIGHT,
            });

            axios.get(downloadUrl).then((response) => {
                toast.update(checkStatus, {
                    render: () => {
                        return (
                            "Success! Download Started!"
                        );
                    },
                    type: toast.TYPE.SUCCESS,
                    isLoading: false,
                    autoClose: 3000,
                });
                window.location.href = response.data.url;
            }).catch((error) => {
                toast.update(checkStatus, {
                    render: () => {
                        return (
                            error.response.data.message ?? "Error - " +
                            error.response.status
                        );
                    },
                    type: toast.TYPE.ERROR,
                    isLoading: false,
                    autoClose: 5000,
                });
            });

        },
        deleteConverted(id) {
            const checkStatus = toast.loading("Please wait...", {
                position: toast.POSITION.TOP_RIGHT,
            });

            axios.delete(`/api/converter/delete/${id}`)
                .then((response) => {
                    toast.update(checkStatus, {
                        render: () => {
                            return (
                                "Success! " + response.data.message
                            );
                        },
                        type: toast.TYPE.WARNING,
                        isLoading: false,
                        autoClose: 3000,
                    });
                    this.getConvertFiles();
                }).catch((error) => {
                    toast.update(checkStatus, {
                        render: () => {
                            return (
                                error.response.data.message ?? "Error - " +
                                error.response.status
                            );
                        },
                        type: toast.TYPE.ERROR,
                        isLoading: false,
                        autoClose: 5000,
                    });
                });
        },
        retryConvert(id) {
            const checkStatus = toast.loading("Please wait...", {
                position: toast.POSITION.TOP_RIGHT,
            });

            axios.post(`/api/converter/retry/${id}`)
                .then((response) => {
                    toast.update(checkStatus, {
                        render: () => {
                            return (
                                "Success! " + response.data.message
                            );
                        },
                        type: toast.TYPE.WARNING,
                        isLoading: false,
                        autoClose: 3000,
                    });
                    this.getConvertFiles();
                }).catch((error) => {
                    toast.update(checkStatus, {
                        render: () => {
                            return (
                                error.response.data.message ?? "Error - " +
                                error.response.status
                            );
                        },
                        type: toast.TYPE.ERROR,
                        isLoading: false,
                        autoClose: 5000,
                    });
                });
        },
        getConvertFiles() {
            this.loadingFiles = true;

            axios.get("/api/converter/list").then((response) => {
                this.convert_files = response.data.files;
            }).catch((error) => {
                toast.error("Error - " + error.response.data.message ??
                    error.response.status, {
                    type: toast.TYPE.ERROR,
                    isLoading: false,
                    autoClose: 5000,
                });
            }).finally(() => {
                this.loadingFiles = false;
            });
        },
        formatOptions(options) {
            const nonNullValues = Object.values(options).filter(value => value !== null);
            return nonNullValues.length > 0 ? nonNullValues.join(' · ') : 'all default';
        }

    }
}

</script>
