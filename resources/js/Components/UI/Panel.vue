<template>
    <section :aria-labelledby="headingId" class="bg-white rounded-3xl shadow-xl p-6" role="region">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4" :class="headerBorder ? 'border-b border-slate-100 pb-5' : ''">
            <div>
                <h2 v-if="title" :id="headingId" class="text-xl font-semibold text-slate-800">{{ title }}</h2>
                <p v-if="description" class="text-sm text-slate-500 mt-1">{{ description }}</p>
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
