<script setup>
const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: Object,
        default: null,
    },
    iconBackground: {
        type: String,
        default: 'bg-sky-500/10 text-sky-600',
    },
});
</script>

<template>
    <div class="flex items-center gap-5 rounded-2xl border border-slate-200 bg-white p-5">
        <div v-if="icon" class="flex h-11 w-11 items-center justify-center rounded-xl" :class="iconBackground">
            <component :is="icon" class="h-6 w-6" />
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ label }}</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ value }}</p>
            <slot name="description" />
        </div>
    </div>
</template>
