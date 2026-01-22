<template>
    <div
        :class="[
            'rounded-2xl border shadow-sm transition-transform duration-200',
            bordered ? 'border-white/15' : 'border-slate-200/70',
            gradient ? 'bg-white/5 text-white' : 'bg-white/80 text-slate-900',
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
        default: 'p-5',
    },
});

const subtitleClass = computed(() => props.gradient ? 'text-emerald-100/80' : 'text-slate-500');
const valueClass = computed(() => props.gradient ? 'text-white' : 'text-slate-900');
const descriptionClass = computed(() => props.gradient ? 'text-emerald-100/70' : 'text-slate-500');

</script>
