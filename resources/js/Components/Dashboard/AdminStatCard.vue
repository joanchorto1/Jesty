<template>
    <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl p-6 text-white shadow-lg">
        <p v-if="label" class="text-xs uppercase tracking-[0.3em] text-blue-200">{{ label }}</p>
        <div class="mt-2 flex items-baseline justify-between gap-4">
            <p class="text-3xl font-semibold">{{ value }}</p>
            <slot name="badge" />
        </div>
        <p v-if="description" class="mt-3 text-sm text-blue-200">{{ description }}</p>
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
