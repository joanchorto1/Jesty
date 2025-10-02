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
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r text-white shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-r opacity-90" :class="accent"></div>
        <div class="relative flex flex-col gap-6 px-8 py-10 md:flex-row md:items-center">
            <div v-if="icon" class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur">
                <component :is="icon" class="h-9 w-9 text-white" />
            </div>
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight md:text-4xl">{{ title }}</h1>
                <p v-if="description" class="mt-3 text-base text-white/80 md:text-lg">
                    {{ description }}
                </p>
            </div>
            <div class="md:ml-auto">
                <slot name="actions" />
            </div>
        </div>
    </section>
</template>
