<template>
    <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
        <p class="text-xs uppercase tracking-[0.3em] text-violet-200">{{ label }}</p>
        <p class="text-3xl font-semibold mt-2">{{ value }}</p>
        <p v-if="hint" class="text-sm text-violet-200 mt-3">{{ hint }}</p>
        <slot />
    </div>
</template>

<script setup>
const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        default: '—',
    },
    hint: {
        type: String,
        default: null,
    },
});
</script>
