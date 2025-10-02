<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-sky-500 via-sky-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-[0.2em] shadow-lg shadow-sky-900/30 hover:from-sky-400 hover:via-sky-500 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-400 transition ease-in-out duration-200"
    >
        <slot />
    </button>
</template>
