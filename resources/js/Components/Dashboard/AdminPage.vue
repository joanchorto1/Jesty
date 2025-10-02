<template>
    <div class="min-h-screen bg-slate-950">
        <div v-if="!flushHeader || $slots.header" :class="headerWrapperClasses">
            <div class="max-w-7xl mx-auto px-6 pt-10">
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
    'pb-24',
    props.flushHeader ? 'bg-slate-950' : 'bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900',
]);

const contentWrapperClasses = computed(() => [
    'max-w-7xl mx-auto px-6 pb-16 space-y-10',
    hasHeader.value ? '-mt-16' : 'pt-10',
]);
</script>
