<template>
    <section class="bg-white rounded-3xl shadow-xl p-6 h-full flex flex-col">
        <header class="space-y-1">
            <p v-if="kicker" class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500/80">{{ kicker }}</p>
            <h2 class="text-xl font-semibold text-slate-800">{{ title }}</h2>
            <p v-if="subtitle" class="text-sm text-slate-500">{{ subtitle }}</p>
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
