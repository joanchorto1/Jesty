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
        wrapper: 'rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg',
        eyebrow: 'text-emerald-200',
        description: 'text-emerald-200',
    },
    light: {
        wrapper: 'rounded-2xl border border-slate-200 bg-white p-6 text-slate-800 shadow-lg',
        eyebrow: 'text-emerald-500',
        description: 'text-slate-500',
    },
};

const wrapperClass = computed(() => variantStyles[props.variant]?.wrapper || variantStyles.glass.wrapper);
const eyebrowClass = computed(() => variantStyles[props.variant]?.eyebrow || variantStyles.glass.eyebrow);
const descriptionClass = computed(() => variantStyles[props.variant]?.description || variantStyles.glass.description);

const hasDescriptionSlot = computed(() => !!useSlots().description);
</script>
