<template>
    <div :class="wrapperClass">
        <p v-if="eyebrow" class="text-xs uppercase tracking-[0.3em]" :class="eyebrowClass">{{ eyebrow }}</p>
        <p class="text-3xl font-semibold mt-2">
            <slot name="value">{{ value }}</slot>
        </p>
        <p v-if="description || hasDescriptionSlot" class="text-sm mt-3" :class="descriptionClass">
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
        wrapper: 'rounded-2xl border border-white/15 bg-white/5 p-5 text-white shadow-sm',
        eyebrow: 'text-emerald-100/80',
        description: 'text-emerald-100/80',
    },
    light: {
        wrapper: 'rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm',
        eyebrow: 'text-emerald-500',
        description: 'text-slate-500',
    },
};

const wrapperClass = computed(() => variantStyles[props.variant]?.wrapper || variantStyles.glass.wrapper);
const eyebrowClass = computed(() => variantStyles[props.variant]?.eyebrow || variantStyles.glass.eyebrow);
const descriptionClass = computed(() => variantStyles[props.variant]?.description || variantStyles.glass.description);

const hasDescriptionSlot = computed(() => !!useSlots().description);
</script>
