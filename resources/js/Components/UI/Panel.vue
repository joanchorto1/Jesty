<template>
    <section :aria-labelledby="headingId" class="rounded-2xl border border-slate-200 bg-white p-6" role="region">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between" :class="headerBorder ? 'border-b border-slate-200 pb-5' : ''">
            <div>
                <h2 v-if="title" :id="headingId" class="text-lg font-semibold text-slate-900">{{ title }}</h2>
                <p v-if="description" class="mt-1 text-sm text-slate-500">{{ description }}</p>
            </div>
            <div>
                <slot name="actions" />
            </div>
        </div>
        <div :class="headerBorder ? 'pt-6' : ''">
            <slot />
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    headerBorder: {
        type: Boolean,
        default: true,
    },
});

const headingId = computed(() => (props.title ? `${props.title.replace(/\s+/g, '-').toLowerCase()}-panel` : undefined));
</script>
