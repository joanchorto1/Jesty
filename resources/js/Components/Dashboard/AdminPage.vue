<template>
    <div class="min-h-screen bg-slate-100/80 py-12">
        <div v-if="!flushHeader || $slots.header" :class="headerWrapperClasses">
            <div class="max-w-7xl mx-auto px-6">
                <slot name="header" />
            </div>
        </div>
        <div :class="contentWrapperClasses">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    flushHeader: {
        type: Boolean,
        default: false,
    },
});

const slots = useSlots();

const hasHeader = computed(() => Boolean(slots.header));

const headerWrapperClasses = computed(() => [
    'mb-10',
    props.flushHeader ? 'bg-transparent' : 'bg-transparent',
]);

const contentWrapperClasses = computed(() => [
    'max-w-7xl mx-auto px-6 pb-16 space-y-10',
    hasHeader.value ? 'pt-0' : 'pt-4',
]);
</script>
