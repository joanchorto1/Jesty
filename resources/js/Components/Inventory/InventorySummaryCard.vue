<template>
    <article class="rounded-3xl border border-amber-500/10 bg-white px-6 py-5 shadow-sm shadow-amber-900/5 transition duration-200 hover:-translate-y-1 hover:shadow-amber-900/20">
        <div class="flex items-start justify-between gap-6">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500/80">{{ label }}</p>
                <p class="text-3xl font-semibold text-slate-900">{{ value }}</p>
                <p v-if="description || hasDescription" class="text-xs text-slate-600">
                    <slot name="description">{{ description }}</slot>
                </p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-200/40 via-yellow-200/40 to-slate-900/20 text-amber-600 ring-1 ring-amber-500/20">
                <slot name="icon" />
            </div>
        </div>
    </article>
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
    description: {
        type: String,
        default: '',
    },
});

const slots = useSlots();
const hasDescription = computed(() => Boolean(slots.description));
</script>
