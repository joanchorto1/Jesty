<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    icon: {
        type: Object,
        default: null,
    },
    accent: {
        type: String,
        default: 'from-sky-500 to-indigo-500',
    },
});
</script>

<template>
    <section class="rounded-2xl border border-slate-200 bg-white">
        <div class="flex flex-col gap-6 px-8 py-8 md:flex-row md:items-center">
            <div v-if="icon" class="flex h-14 w-14 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                <component :is="icon" class="h-7 w-7" />
            </div>
            <div class="max-w-3xl">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 md:text-3xl">{{ title }}</h1>
                <p v-if="description" class="mt-2 text-sm text-slate-500 md:text-base">
                    {{ description }}
                </p>
            </div>
            <div class="md:ml-auto">
                <slot name="actions" />
            </div>
        </div>
    </section>
</template>
