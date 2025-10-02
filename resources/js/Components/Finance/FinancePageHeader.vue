<template>
    <div class="bg-gradient-to-r from-slate-900 via-emerald-700 to-blue-900 pb-24 print:bg-white print:pb-10">
        <div class="max-w-7xl mx-auto px-6 pt-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-2 text-white print:text-slate-900">
                    <p v-if="eyebrow" class="text-emerald-200 text-xs uppercase tracking-[0.35em] print:text-emerald-700">
                        {{ eyebrow }}
                    </p>
                    <h1 class="text-3xl sm:text-4xl font-semibold">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-emerald-200 max-w-2xl print:text-slate-600">
                        {{ description }}
                    </p>
                </div>
                <div v-if="$slots.actions" class="flex flex-wrap gap-3 print:hidden">
                    <slot name="actions" />
                </div>
            </div>

            <div v-if="$slots.metrics" class="mt-10">
                <div :class="metricsGridClass">
                    <slot name="metrics" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    eyebrow: {
        type: String,
        default: '',
    },
    metricsColumns: {
        type: Number,
        default: 4,
    },
});

const metricsGridClass = computed(() => {
    const base = 'grid grid-cols-1 gap-5';
    const variants = {
        1: `${base}`,
        2: `${base} sm:grid-cols-2`,
        3: `${base} sm:grid-cols-2 xl:grid-cols-3`,
        4: `${base} sm:grid-cols-2 xl:grid-cols-4`,
        5: `${base} sm:grid-cols-2 xl:grid-cols-5`,
    };

    return variants[props.metricsColumns] ?? variants[4];
});
</script>
