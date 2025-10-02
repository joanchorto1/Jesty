<template>
    <div class="overflow-x-auto pt-6">
        <table class="w-full text-left" :aria-describedby="descriptionId">
            <caption v-if="caption" :id="descriptionId" class="sr-only">{{ caption }}</caption>
            <thead>
                <tr class="text-xs uppercase tracking-widest text-slate-400">
                    <th v-for="column in columns" :key="column.key" class="pb-3" :class="column.align === 'right' ? 'text-right' : column.align === 'center' ? 'text-center' : ''">
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <template v-if="items.length">
                    <tr v-for="item in items" :key="resolveKey(item)" class="hover:bg-slate-50/80 transition" tabindex="0">
                        <slot name="row" :item="item" />
                    </tr>
                </template>
                <tr v-else>
                    <td :colspan="columns.length" class="py-6 text-center text-slate-400">
                        <slot name="empty">
                            {{ emptyMessage }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    columns: {
        type: Array,
        default: () => [],
    },
    items: {
        type: Array,
        default: () => [],
    },
    emptyMessage: {
        type: String,
        default: 'No hay datos disponibles.',
    },
    rowKey: {
        type: [String, Function],
        default: 'id',
    },
    caption: {
        type: String,
        default: '',
    },
});

const descriptionId = computed(() => (props.caption ? `${props.caption.replace(/\s+/g, '-').toLowerCase()}-caption` : undefined));

function resolveKey(item) {
    if (typeof props.rowKey === 'function') {
        return props.rowKey(item);
    }
    return item?.[props.rowKey];
}
</script>
