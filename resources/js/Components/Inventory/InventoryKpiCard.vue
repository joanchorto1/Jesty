<template>
    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-500">{{ label }}</p>
        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ value }}</p>
        <p v-if="subtitle || hasSubtitleSlot" class="mt-3 text-sm text-slate-500">
            <slot name="subtitle">{{ subtitle }}</slot>
        </p>
        <slot />
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';

defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
});

const slots = useSlots();
const hasSubtitleSlot = computed(() => Boolean(slots.subtitle));
</script>
