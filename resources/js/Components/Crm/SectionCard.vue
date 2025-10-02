<template>
    <div class="bg-white rounded-3xl shadow-xl p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">{{ title }}</h2>
                <p v-if="description" class="text-sm text-slate-500 mt-1">{{ description }}</p>
            </div>
            <div class="flex items-center gap-2" v-if="$slots.actions">
                <slot name="actions" />
            </div>
        </div>
        <div class="mt-6">
            <slot />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: null,
    },
});
</script>
