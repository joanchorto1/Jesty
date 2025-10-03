<template>
    <div class="rounded-3xl border border-amber-500/30 bg-white/10 backdrop-blur p-6 text-white shadow-lg shadow-amber-900/20 transition duration-200 hover:-translate-y-1 hover:shadow-2xl">
        <p class="text-xs uppercase tracking-[0.3em] text-amber-200/90">{{ label }}</p>
        <p class="mt-2 text-3xl font-semibold text-white">{{ value }}</p>
        <p v-if="subtitle || hasSubtitleSlot" class="mt-3 text-sm text-amber-100/80">
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
