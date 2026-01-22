<template>
    <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-6 text-slate-800 shadow-sm">
        <p v-if="label" class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ label }}</p>
        <div class="mt-2 flex items-baseline justify-between gap-4">
            <p class="text-3xl font-semibold">{{ value }}</p>
            <slot name="badge" />
        </div>
        <p v-if="description" class="mt-3 text-sm text-slate-500">{{ description }}</p>
        <slot />
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: '',
    },
    value: {
        type: [String, Number],
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
});
</script>
