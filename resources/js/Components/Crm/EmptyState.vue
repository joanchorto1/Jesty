<template>
    <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-200 bg-slate-50 py-16 px-8 text-center">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-violet-500/10 text-violet-500">
            <slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm10.5-4.125a.75.75 0 10-1.5 0v4.5a.75.75 0 001.5 0v-4.5ZM12 16.5a.875.875 0 100-1.75.875.875 0 000 1.75Z" clip-rule="evenodd" />
                </svg>
            </slot>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-700">{{ title }}</h3>
        <p v-if="description" class="mt-2 max-w-md text-sm text-slate-500">{{ description }}</p>
        <div v-if="$slots.action" class="mt-6">
            <slot name="action" />
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
