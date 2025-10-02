<template>
    <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur p-6 text-white shadow-lg transition hover:border-emerald-200/50 print:border-slate-200 print:bg-white print:text-slate-900 print:shadow-none">
        <p v-if="label" class="text-xs uppercase tracking-[0.35em] text-emerald-200 print:text-emerald-600">
            {{ label }}
        </p>
        <div class="mt-3">
            <slot name="value">
                <p class="text-3xl font-semibold">
                    {{ value }}
                </p>
            </slot>
        </div>
        <p v-if="helper" class="text-sm text-emerald-200 mt-3 print:text-slate-500">
            {{ helper }}
        </p>
        <slot />
    </div>
</template>

<script setup>
const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    value: {
        type: [String, Number],
        default: '',
    },
    helper: {
        type: String,
        default: '',
    },
});
</script>
