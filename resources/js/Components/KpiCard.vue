<template>
    <div
        :class="[
            'rounded-2xl border transition-transform duration-200',
            bordered ? 'border-slate-200' : 'border-slate-200',
            gradient ? 'bg-slate-50 text-slate-900' : 'bg-white text-slate-900',
            padding,
        ]"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em]" :class="subtitleClass">{{ subtitle }}</p>
                <p class="mt-2 text-3xl font-semibold" :class="valueClass">{{ value }}</p>
            </div>
            <slot name="icon" />
        </div>
        <p class="mt-3 text-sm" :class="descriptionClass">{{ description }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    subtitle: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    gradient: {
        type: Boolean,
        default: false,
    },
    bordered: {
        type: Boolean,
        default: true,
    },
    padding: {
        type: String,
        default: 'p-6',
    },
});

const subtitleClass = computed(() => props.gradient ? 'text-slate-500' : 'text-slate-500');
const valueClass = computed(() => props.gradient ? 'text-slate-900' : 'text-slate-900');
const descriptionClass = computed(() => props.gradient ? 'text-slate-500' : 'text-slate-500');
</script>
