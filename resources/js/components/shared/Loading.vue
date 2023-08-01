<template>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" :class="`tw-h-${size} tw-w-${size}`"
            viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
            <circle cx="50" cy="50" r="32" stroke-width="8" stroke="currentColor"
                stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.9615384615384615s"
                    keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
            </circle>
        </svg>
    </div>
</template>

<script>
export default {
    name: 'Loading',
    props: {
        size: {
            type: Number,
            default: 5
        },
    }
};
</script>
