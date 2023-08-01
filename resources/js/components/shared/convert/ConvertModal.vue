<template>
    <div class="tw-relative tw-z-10">
        <div class="tw-fixed tw-inset-0 tw-bg-gray-900 tw-backdrop-blur tw-bg-opacity-50 tw-transition-opacity"></div>
        <div class="tw-fixed tw-inset-0 tw-z-10 tw-overflow-y-auto">
            <div
                class="tw-flex tw-min-h-full tw-items-end tw-justify-center tw-p-4 tw-text-center sm:tw-items-center sm:tw-p-0">
                <div
                    class="tw-relative tw-transform tw-overflow-hidden tw-rounded-lg tw-bg-white tw-text-left tw-shadow-xl tw-transition-all sm:tw-my-8 sm:tw-w-full sm:tw-max-w-3xl dark:tw-bg-gray-900">
                    <div class="tw-bg-white tw-px-4 tw-pb-4 tw-pt-5 sm:tw-p-6 sm:tw-pb-4 dark:tw-bg-gray-800">
                        <div class="sm:tw-flex sm:tw-items-start">
                            <div :class="!loading ? 'tw-bg-gray-200/75 dark:tw-bg-gray-600/75' : 'tw-bg-gray-200/10 dark:tw-bg-gray-600/10'"
                                class="tw-mx-auto tw-flex tw-h-12 tw-w-12 tw-flex-shrink-0 tw-items-center tw-justify-center tw-rounded-full  sm:tw-mx-0 sm:tw-h-10 sm:tw-w-10">
                                <Loading v-if="loading" class="tw-text-primary-600" :size="8" />
                                <svg v-else class="tw-h-6 tw-w-6 tw-text-gray-900 dark:tw-text-gray-200" fill="currentColor"
                                    viewBox="0 0 24 24" stroke="none" aria-hidden="true">
                                    <path
                                        d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z">
                                    </path>
                                    <path
                                        d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z">
                                    </path>
                                </svg>
                            </div>
                            <div class="tw-mt-3 tw-w-full tw-text-center sm:tw-ml-4 sm:tw-mt-0 sm:tw-text-left">
                                <h3 class="tw-text-base tw-font-semibold tw-leading-6 tw-text-gray-900 dark:tw-text-gray-200"
                                    id="modal-title">Convert to .{{ convert_file.extension }}</h3>
                                <p class="tw-text-sm tw-text-gray-500 dark:tw-text-gray-300">You can customize some options
                                    here :D</p>

                                <div class="tw-mt-5">
                                    <div>
                                        <p class="tw-mb-1 tw-text-sm tw-text-gray-500 dark:tw-text-gray-300">Resolution</p>
                                        <select v-model="selectedResolution"
                                            class="tw-py-3 tw-px-4 tw-pr-9 tw-block tw-w-full tw-border-gray-200 tw-rounded-md tw-text-sm focus:tw-border-blue-500 focus:tw-ring-blue-500 dark:tw-bg-gray-900 dark:tw-border-gray-700 dark:tw-text-gray-400">
                                            <option value="" selected>Current</option>
                                            <option v-for="(resolution) in options.resolutions" :key="resolution"
                                                :value="resolution">{{
                                                    resolution }}</option>
                                        </select>
                                    </div>
                                    <div class="tw-mt-3 tw-gap-2 tw-grid lg:tw-grid-cols-2">
                                        <div>
                                            <p class="tw-mb-1 tw-text-sm tw-text-gray-500 dark:tw-text-gray-300">Video Codec
                                            </p>
                                            <select v-model="selectedCodecVideo"
                                                class="tw-py-3 tw-px-4 tw-pr-9 tw-block tw-w-full tw-border-gray-200 tw-rounded-md tw-text-sm focus:tw-border-blue-500 focus:tw-ring-blue-500 dark:tw-bg-gray-900 dark:tw-border-gray-700 dark:tw-text-gray-400">
                                                <option value="" selected>Current</option>
                                                <option v-for="(key, codec) in options.video" :key="key" :value="key">{{
                                                    codec }}</option>
                                            </select>
                                        </div>

                                        <div>
                                            <p class="tw-mb-1 tw-text-sm tw-text-gray-500 dark:tw-text-gray-300">Audio Codec
                                            </p>
                                            <select v-model="selectedCodecAudio"
                                                class="tw-py-3 tw-px-4 tw-pr-9 tw-block tw-w-full tw-border-gray-200 tw-rounded-md tw-text-sm focus:tw-border-blue-500 focus:tw-ring-blue-500 dark:tw-bg-gray-900 dark:tw-border-gray-700 dark:tw-text-gray-400">
                                                <option value="" selected>Current</option>
                                                <option v-for="(key, codec) in options.audio" :key="key" :value="key">{{
                                                    codec }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="tw-bg-gray-200 tw-px-4 tw-py-3 sm:tw-flex sm:tw-flex-row-reverse sm:tw-px-6 dark:tw-bg-gray-900">
                        <button @click="this.convertFile()"
                            class="tw-transition-all tw-duration-300 tw-px-2.5 tw-py-2 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-primary-700 tw-rounded-lg tw-border tw-border-primary-700 hover:tw-bg-primary-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-primary-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-primary-600 dark:hover:tw-bg-primary-700 dark:focus:tw-ring-primary-600"
                            type="button">Convert <svg class="tw-w-4 tw-h-4  tw-ml-2" viewBox="0 0 16 16"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor"
                                    d="M14.9547098,7.98576084 L15.0711,7.99552 C15.6179,8.07328 15.9981,8.57957 15.9204,9.12636 C15.6826,10.7983 14.9218,12.3522 13.747,13.5654 C12.5721,14.7785 11.0435,15.5888 9.37999,15.8801 C7.7165,16.1714 6.00349,15.9288 4.48631,15.187 C3.77335,14.8385 3.12082,14.3881 2.5472,13.8537 L1.70711,14.6938 C1.07714,15.3238 3.55271368e-15,14.8776 3.55271368e-15,13.9867 L3.55271368e-15,9.99998 L3.98673,9.99998 C4.87763,9.99998 5.3238,11.0771 4.69383,11.7071 L3.9626,12.4383 C4.38006,12.8181 4.85153,13.1394 5.36475,13.3903 C6.50264,13.9466 7.78739,14.1285 9.03501,13.9101 C10.2826,13.6916 11.4291,13.0839 12.3102,12.174 C13.1914,11.2641 13.762,10.0988 13.9403,8.84476 C14.0181,8.29798 14.5244,7.91776 15.0711,7.99552 L14.9547098,7.98576084 Z M11.5137,0.812976 C12.2279,1.16215 12.8814,1.61349 13.4558,2.14905 L14.2929,1.31193 C14.9229,0.681961 16,1.12813 16,2.01904 L16,6.00001 L12.019,6.00001 C11.1281,6.00001 10.6819,4.92287 11.3119,4.29291 L12.0404,3.56441 C11.6222,3.18346 11.1497,2.86125 10.6353,2.60973 C9.49736,2.05342 8.21261,1.87146 6.96499,2.08994 C5.71737,2.30841 4.57089,2.91611 3.68976,3.82599 C2.80862,4.73586 2.23802,5.90125 2.05969,7.15524 C1.98193,7.70202 1.47564,8.08224 0.928858,8.00448 C0.382075,7.92672 0.00185585,7.42043 0.0796146,6.87364 C0.31739,5.20166 1.07818,3.64782 2.25303,2.43465 C3.42788,1.22148 4.95652,0.411217 6.62001,0.119916 C8.2835,-0.171384 9.99651,0.0712178 11.5137,0.812976 Z" />
                            </svg>
                        </button>
                        <button @click="this.$emit('close')"
                            class="tw-mr-2 tw-transition-all tw-duration-300 tw-px-2.5 tw-py-2 tw-items-center tw-flex tw-h-full tw-text-sm tw-font-medium tw-text-white tw-bg-gray-700 tw-rounded-lg tw-border tw-border-gray-700 hover:tw-bg-gray-800 focus:tw-ring-2 focus:tw-ring-offset-2 tw-outline-none focus:tw-ring-gray-300 dark:focus:tw-ring-offset-slate-800 dark:tw-bg-gray-600 dark:hover:tw-bg-gray-700 dark:focus:tw-ring-gray-600"
                            type="button">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Loading from "../../shared/Loading.vue"
import { toast } from "vue3-toastify";

export default {
    name: 'ConvertModal',
    props: {
        convert_file: {
            type: Object,
            default: () => ({})
        },
    },
    components: {
        Loading
    },
    data() {
        return {
            loading: false,
            options: [],
            selectedResolution: '',
            selectedCodecVideo: '',
            selectedCodecAudio: '',
        }
    },
    mounted() {
        this.loadMetadata();
    },

    methods: {
        loadMetadata() {
            this.loading = true;

            axios.get(`api/converter/options/${this.convert_file.id}/to/${this.convert_file.extension}`)
                .then((response) => {
                    this.options = response.data.options;
                }).catch((error) => {
                    toast.error("Error - " + error.response.data.message ??
                        error.response.status, {
                        type: toast.TYPE.ERROR,
                        isLoading: false,
                        autoClose: 5000,
                    });
                }).finally(() => {
                    this.loading = false;
                });
        },

        convertFile() {
            const checkStatus = toast.loading("Please wait...", {
                position: toast.POSITION.TOP_RIGHT,
            });

            this.loading = true;

            const options = {
                'resolution': this.selectedResolution,
                'codec_video': this.selectedCodecVideo,
                'codec_audio': this.selectedCodecAudio
            }

            axios.post(`api/converter/convert/${this.convert_file.id}/to/${this.convert_file.extension}`, { options })
                .then((response) => {
                    this.options = response.data.options;
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
                    this.$emit('close');
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
                    this.loading = false;
                });
        },
    }
};
</script>
