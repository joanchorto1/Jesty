<template>
    <section class="flex h-full flex-col rounded-3xl bg-white p-6 shadow-xl shadow-amber-900/10 ring-1 ring-amber-500/10">
        <header class="space-y-1">
            <p v-if="kicker" class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500/80">{{ kicker }}</p>
            <h2 class="text-xl font-semibold text-slate-900">{{ title }}</h2>
            <p v-if="subtitle" class="text-sm text-slate-600">{{ subtitle }}</p>
        </header>
        <div class="mt-6 flex-1">
            <slot />
        </div>
    </section>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
    kicker: {
        type: String,
        default: '',
    },
});
</script>
