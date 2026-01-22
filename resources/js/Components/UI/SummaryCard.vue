<template>
    <div :class="wrapperClass">
        <p v-if="eyebrow" class="text-xs uppercase tracking-[0.3em]" :class="eyebrowClass">{{ eyebrow }}</p>
        <p class="mt-2 text-3xl font-semibold">
            <slot name="value">{{ value }}</slot>
        </p>
        <p v-if="description || hasDescriptionSlot" class="mt-3 text-sm" :class="descriptionClass">
            <slot name="description">{{ description }}</slot>
        </p>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    value: {
        type: [String, Number],
        default: '',
    },
    eyebrow: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'glass',
    },
});

const variantStyles = {
    glass: {
        wrapper: 'rounded-2xl border border-slate-200 bg-white p-6 text-slate-800',
        eyebrow: 'text-slate-500',
        description: 'text-slate-500',
    },
    light: {
        wrapper: 'rounded-2xl border border-slate-200 bg-white p-6 text-slate-800',
        eyebrow: 'text-slate-500',
        description: 'text-slate-500',
    },
};

const wrapperClass = computed(() => variantStyles[props.variant]?.wrapper || variantStyles.glass.wrapper);
const eyebrowClass = computed(() => variantStyles[props.variant]?.eyebrow || variantStyles.glass.eyebrow);
const descriptionClass = computed(() => variantStyles[props.variant]?.description || variantStyles.glass.description);

const hasDescriptionSlot = computed(() => !!useSlots().description);
</script>
