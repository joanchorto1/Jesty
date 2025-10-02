<template>
    <span :class="['inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset', statusClasses]">
        <span v-if="showDot" class="h-2 w-2 rounded-full bg-current"></span>
        <slot>{{ statusLabel }}</slot>
    </span>
</template>

<script setup>
import { computed } from 'vue';
import { resolveInventoryStatus } from './inventoryTheme';

const props = defineProps({
    status: {
        type: [String, Number],
        default: '',
    },
    fallback: {
        type: String,
        default: '—',
    },
    showDot: {
        type: Boolean,
        default: true,
    },
});

const meta = computed(() => resolveInventoryStatus(props.status, props.fallback));
const statusLabel = computed(() => meta.value.label);
const statusClasses = computed(() => meta.value.classes);
</script>
