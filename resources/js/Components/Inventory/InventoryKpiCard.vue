<template>
    <div class="rounded-3xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg shadow-amber-900/10 transition duration-200 hover:-translate-y-1 hover:shadow-2xl">
        <p class="text-xs uppercase tracking-[0.3em] text-amber-200">{{ label }}</p>
        <p class="text-3xl font-semibold mt-2 text-white">{{ value }}</p>
        <p v-if="subtitle || hasSubtitleSlot" class="text-sm text-amber-200 mt-3">
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
