<template>
    <div class="rounded-2xl border border-amber-200/30 bg-white/5 p-5 text-white shadow-sm">
        <p class="text-xs uppercase tracking-[0.3em] text-amber-100/80">{{ label }}</p>
        <p class="mt-2 text-3xl font-semibold text-white">{{ value }}</p>
        <p v-if="subtitle || hasSubtitleSlot" class="mt-3 text-sm text-amber-100/70">
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
