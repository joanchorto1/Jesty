<template>
    <div class="rounded-3xl bg-white shadow-xl ring-1 ring-slate-900/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <slot name="head" />
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <slot />
                </tbody>
            </table>
        </div>
        <footer v-if="hasFooter" class="border-t border-slate-100 bg-slate-50/60 px-6 py-4">
            <slot name="footer" />
        </footer>
        <div v-if="showEmptyState" class="px-6 py-10 text-center text-sm text-slate-500">
            <slot name="empty">No hi ha registres disponibles.</slot>
        </div>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    isEmpty: {
        type: Boolean,
        default: false,
    },
});

const slots = useSlots();

const hasFooter = computed(() => Boolean(slots.footer));
const showEmptyState = computed(() => props.isEmpty);
</script>
