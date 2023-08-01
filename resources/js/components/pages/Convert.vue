<template>
    <div>
        <div class="tw-flex tw-flex-col tw-items-center tw-justify-center">
            <p class="tw-text-lg tw-font-semibold tw-tracking-widest tw-text-gray-800 tw-uppercase dark:tw-text-gray-200">
                CONVERT A NEW FILE</p>

            <div class="tw-w-[90%] tw-mt-2 tw-relative lg:tw-w-[70%]">
                <input type="url" v-model="downloadUrlVideo"
                    class="tw-transition-all tw-duration-300 tw-block tw-rounded-l-md tw-p-4 tw-w-full tw-text-sm tw-text-gray-900 tw-bg-gray-50 tw-border-2 tw-rounded-r-lg border-l-gray-100 tw-border-gray-300 tw-outline-none focus:tw-border-primary-500 dark:tw-bg-gray-700 dark:tw-border-gray-600 dark:tw-placeholder-gray-400 dark:tw-text-white dark:focus:tw-border-primary-500"
                    placeholder="http(s)://example.com/video.mkv">
                <button type="submit" @click="convertUrl"
                    class="tw-transition-all tw-duration-300 tw-absolute tw-top-0 tw-right-0 tw-p-4 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-primary-700 tw-rounded-r-lg tw-border tw-border-primary-700 hover:tw-bg-primary-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-primary-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-primary-600 dark:hover:tw-bg-primary-700 dark:focus:tw-ring-primary-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="tw-w-8 tw-h-8" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13 19v-4h3l-4-5-4 5h3v4z"></path>
                        <path
                            d="M7 19h2v-2H7c-1.654 0-3-1.346-3-3 0-1.404 1.199-2.756 2.673-3.015l.581-.102.192-.558C8.149 8.274 9.895 7 12 7c2.757 0 5 2.243 5 5v1h1c1.103 0 2 .897 2 2s-.897 2-2 2h-3v2h3c2.206 0 4-1.794 4-4a4.01 4.01 0 0 0-3.056-3.888C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.611 5.757 9.15 3.609 9.792 2 11.82 2 14c0 2.757 2.243 5 5 5z">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="tw-w-[92%] tw-mt-5 lg:tw-w-[90%]">
                <div v-if="loadingFiles" class="tw-flex tw-justify-center tw-justify-items-center">
                    <Loading class="tw-text-primary-600" :size="8" />
                </div>

                <ul v-else-if="files.length > 0" role="list" class="tw-space-y-2 tw-overflow-y-auto tw-h-64">
                    <li v-for="(file, index) in files" :key="index"
                        class="tw-py-3 tw-bg-slate-100 tw-p-4 tw-rounded-lg sm:tw-py-4 lg:tw-mr-2 dark:tw-bg-gray-900">
                        <div class="tw-flex tw-items-center tw-space-x-4">
                            <div class="tw-flex-shrink-0">
                                <StatusIcon :status="file.status" size="8" />
                            </div>
                            <div class="tw-flex-1 tw-min-w-0">
                                <div class="tw-flex">
                                    <p
                                        class="tw-max-w-[30%] tw-text-sm tw-font-medium tw-text-gray-900 tw-truncate dark:tw-text-gray-200">
                                        {{ file.original_name }}
                                    </p>
                                    <p class="tw-mx-2 tw-text-sm tw-font-bold tw-text-gray-900 dark:tw-text-gray-200">
                                        &middot;
                                    </p>
                                    <p
                                        class="tw-max-w-[30%] tw-text-sm tw-font-medium tw-text-gray-900 tw-truncate dark:tw-text-gray-200">
                                        {{ file.original_url }}
                                    </p>
                                </div>
                                <ProgressBar :status="file.status" :progress="file.progress" />
                            </div>
                            <div v-if="file.status == 'COMPLETED'">
                                <button @click="toggleDropdown(file.id, index)"
                                    class="tw-transition-all tw-duration-300 tw-px-2.5 tw-py-2 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-primary-700 tw-rounded-lg tw-border tw-border-primary-700 hover:tw-bg-primary-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-primary-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-primary-600 dark:hover:tw-bg-primary-700 dark:focus:tw-ring-primary-600"
                                    type="button">Convert<svg class="tw-w-2.5 tw-h-2.5 tw-ml-2.5" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg></button>
                                <div v-if="loadingDropdown == file.id">
                                    <div id="dropdown"
                                        class="tw-z-10 tw-bg-white tw-flex tw-items-center tw-justify-center tw-mt-3 tw-mb-4 tw-absolute tw-divide-y tw-divide-gray-100 tw-rounded-lg tw-shadow dark:tw-bg-slate-800">
                                        <ul class="tw-py-2 tw-px-8 tw-text-sm tw-text-gray-700 dark:tw-text-gray-200">
                                            <Loading class="tw-text-primary-600" :size="8" />
                                        </ul>
                                    </div>
                                </div>
                                <div v-else-if="currentDropdown == file.id && loadingDropdown !== file.id" id="dropdown"
                                    class="tw-z-10 tw-bg-white tw-mt-3 tw-mb-4 tw-absolute tw-divide-y tw-divide-gray-100 tw-rounded-lg tw-shadow dark:tw-bg-slate-800">
                                    <ul
                                        class="tw-shadow-lg tw-py-2 tw-px-2 tw-text-sm tw-text-gray-700 dark:tw-text-gray-200 dark:tw-shadow-gray-900">
                                        <div
                                            v-if="file.avaiableConvertions != null && file.avaiableConvertions.length == 0">
                                            <div class="tw-block tw-rounded-lg tw-px-4 tw-py-2">
                                                No convertion available for file</div>
                                        </div>
                                        <li v-else v-for="convertion in file.avaiableConvertions">
                                            <div @click="selectConvert(file.id, convertion)"
                                                class="tw-cursor-pointer tw-block tw-rounded-lg tw-px-4 tw-py-2 hover:tw-bg-gray-100 dark:hover:dark:tw-bg-slate-900">
                                                to .{{ convertion }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div v-if="file.status == 'FAILED'">
                                <button type="button" @click="retryFile(file.id)"
                                    class="tw-transition-all tw-duration-300 tw-p-1.5 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-yellow-600 tw-rounded-lg tw-border tw-border-yellow-600 hover:tw-bg-yellow-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-yellow-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-yellow-600 dark:hover:tw-bg-yellow-700 dark:focus:tw-ring-yellow-600">
                                    <svg class="tw-w-4 tw-h-4 tw-m-1" viewBox="0 0 16 16"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill="currentColor"
                                            d="M14.9547098,7.98576084 L15.0711,7.99552 C15.6179,8.07328 15.9981,8.57957 15.9204,9.12636 C15.6826,10.7983 14.9218,12.3522 13.747,13.5654 C12.5721,14.7785 11.0435,15.5888 9.37999,15.8801 C7.7165,16.1714 6.00349,15.9288 4.48631,15.187 C3.77335,14.8385 3.12082,14.3881 2.5472,13.8537 L1.70711,14.6938 C1.07714,15.3238 3.55271368e-15,14.8776 3.55271368e-15,13.9867 L3.55271368e-15,9.99998 L3.98673,9.99998 C4.87763,9.99998 5.3238,11.0771 4.69383,11.7071 L3.9626,12.4383 C4.38006,12.8181 4.85153,13.1394 5.36475,13.3903 C6.50264,13.9466 7.78739,14.1285 9.03501,13.9101 C10.2826,13.6916 11.4291,13.0839 12.3102,12.174 C13.1914,11.2641 13.762,10.0988 13.9403,8.84476 C14.0181,8.29798 14.5244,7.91776 15.0711,7.99552 L14.9547098,7.98576084 Z M11.5137,0.812976 C12.2279,1.16215 12.8814,1.61349 13.4558,2.14905 L14.2929,1.31193 C14.9229,0.681961 16,1.12813 16,2.01904 L16,6.00001 L12.019,6.00001 C11.1281,6.00001 10.6819,4.92287 11.3119,4.29291 L12.0404,3.56441 C11.6222,3.18346 11.1497,2.86125 10.6353,2.60973 C9.49736,2.05342 8.21261,1.87146 6.96499,2.08994 C5.71737,2.30841 4.57089,2.91611 3.68976,3.82599 C2.80862,4.73586 2.23802,5.90125 2.05969,7.15524 C1.98193,7.70202 1.47564,8.08224 0.928858,8.00448 C0.382075,7.92672 0.00185585,7.42043 0.0796146,6.87364 C0.31739,5.20166 1.07818,3.64782 2.25303,2.43465 C3.42788,1.22148 4.95652,0.411217 6.62001,0.119916 C8.2835,-0.171384 9.99651,0.0712178 11.5137,0.812976 Z" />
                                    </svg>
                                </button>
                            </div>

                            <div v-else>
                                <button type="button" @click="deleteFile(file.id)"
                                    class="tw-transition-all tw-duration-300 tw-p-1.5 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-red-600 tw-rounded-lg tw-border tw-border-red-600 hover:tw-bg-red-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-red-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-red-600 dark:hover:tw-bg-red-700 dark:focus:tw-ring-red-600">
                                    <svg v-if="file.status == 'COMPLETED' || file.status == 'FAILED'" class="tw-w-6 tw-h-6"
                                        fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0">
                                        </path>
                                    </svg>
                                    <svg v-else class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
                <div v-else>
                    <p class="tw-text-center tw-font-medium tw-text-gray-800 dark:tw-text-gray-200">
                        Nothing found :( Import a new file!
                    </p>
                </div>
            </div>
        </div>

        <ConvertModal :convert_file="currentModal" @close="currentModal = []; currentDropdown = null"
            v-if="currentModal.id" />
    </div>
</template>

<script>
import { toast } from "vue3-toastify";
import ConvertModal from "../shared//convert/ConvertModal.vue"
import Loading from "../shared/Loading.vue"
import StatusIcon from "../shared/StatusIcon.vue"
import ProgressBar from "../shared/ProgressBar.vue"

export default {
    components: {
        ConvertModal,
        Loading,
        StatusIcon,
        ProgressBar,
    },
    data() {
        return {
            downloadUrlVideo: 'https://filesamples.com/samples/video/mkv/sample_960x400_ocean_with_audio.mkv',
            loadingFiles: false,
            files: [],
            currentDropdown: null,
            loadingDropdown: null,
            currentModal: []
        }
    },

    async mounted() {
        this.getFiles();

        const user = await this.$authController.getAuthUser();

        Echo.private(`Downloader.Progress.${user.id}`)
            .listen('.Downloader.Progress', (e) => {
                this.updateProgress(e);
            });
    },

    methods: {
        selectConvert(id, extension) {
            this.currentModal = {
                'id': id,
                'extension': extension,
            }
        },

        updateProgress(item) {
            const index = this.files.findIndex(file => file.id === item.id)
            if (index !== -1) {
                const updatedItem = {
                    ...this.files[index],
                    status: item.status,
                    progress: item.progress,
                    last_update: item.last_update,
                };

                this.files[index] = updatedItem;
            }
        },

        convertUrl() {
            if (!this.checkValid()) {
                toast.error("Invalid Url", {
                    position: toast.POSITION.TOP_RIGHT,
                });
                return;
            }

            this.newFile();
        },

        getFiles() {
            this.loadingFiles = true;

            axios.get("/api/downloader/list", {
                url: this.downloadUrlVideo
            }).then((response) => {
                this.files = response.data;
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

        newFile() {
            const checkStatus = toast.loading("Please wait...", {
                position: toast.POSITION.TOP_RIGHT,
            });

            axios.post("/api/downloader/new", {
                url: this.downloadUrlVideo
            }).then((response) => {
                toast.update(checkStatus, {
                    render: () => {
                        return (
                            "Success! " + response.data.message
                        );
                    },
                    type: toast.TYPE.SUCCESS,
                    isLoading: false,
                    autoClose: 3000,
                });
                this.downloadUrlVideo = null;
                this.getFiles();
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

        retryFile(id) {
            const checkStatus = toast.loading("Please wait...", {
                position: toast.POSITION.TOP_RIGHT,
            });

            axios.delete(`/api/downloader/retry/${id}`)
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
                    this.getFiles();
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

        deleteFile(id) {
            const checkStatus = toast.loading("Please wait...", {
                position: toast.POSITION.TOP_RIGHT,
            });

            axios.delete(`/api/downloader/delete/${id}`)
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
                    this.getFiles();
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

        toggleDropdown(id, index) {
            this.currentDropdown = (this.currentDropdown === id) ? null : id;
            if (this.files[index].avaiableConvertions == null) {
                this.getAvailableConverts(id, index)
            }
        },

        async getAvailableConverts(id, index) {
            this.loadingDropdown = id;
            axios.get(`/api/file/convert/available/${id}`).then((response) => {
                this.files[index].avaiableConvertions = response.data.available;
            }).catch((error) => {
                toast.update(checkStatus, {
                    render: () => {
                        return (
                            "Error - " + error.response.data.message ??
                            error.response.status
                        );
                    },
                    type: toast.TYPE.ERROR,
                    isLoading: false,
                    autoClose: 5000,
                });
            }).finally(() => {
                this.loadingDropdown = null;
            });
        },

        checkValid() {
            var urlRegex = /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/gi;
            return urlRegex.test(this.downloadUrlVideo)
        }
    }
}

</script>
