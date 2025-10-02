<template>
    <section class="bg-white rounded-3xl shadow-xl">
        <header v-if="hasHeader" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 px-6 pt-6 pb-5">
            <div>
                <h2 v-if="title" class="text-xl font-semibold text-slate-800">{{ title }}</h2>
                <p v-if="description" class="mt-1 text-sm text-slate-500">{{ description }}</p>
                <slot name="subtitle" />
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <slot name="actions" />
            </div>
        </header>
        <div :class="bodyClasses">
            <slot />
        </div>
    </section>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    padding: {
        type: String,
        default: 'px-6 pb-6',
    },
});

const slots = useSlots();

const hasHeader = computed(() => Boolean(
    props.title ||
    props.description ||
    slots.subtitle ||
    slots.actions
));

const bodyClasses = computed(() => [
    props.padding,
    'pt-6',
]);
</script>
