<template>
    <section class="rounded-3xl border border-slate-200/60 bg-white/80 backdrop-blur px-6 py-5 shadow-sm">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end">
            <div class="grid flex-1 gap-4" :class="gridClass">
                <slot />
            </div>
            <div class="flex items-center gap-3">
                <slot name="actions" />
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    columns: {
        type: Number,
        default: 3,
    },
});

const gridClass = computed(() => {
    const column = Math.min(Math.max(props.columns, 1), 4);
    switch (column) {
    case 1:
        return 'sm:grid-cols-1';
    case 2:
        return 'sm:grid-cols-2';
    case 4:
        return 'sm:grid-cols-2 lg:grid-cols-4';
    default:
        return 'sm:grid-cols-2 lg:grid-cols-3';
    }
});
</script>
