<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'sub'].includes(value),
    },
});

const classes = computed(() => {
    const base = 'group flex w-full items-center justify-between gap-3 transition-all duration-200 ease-out';
    const ring = 'ring-1 ring-transparent';

    const variantBase = props.variant === 'sub'
        ? 'rounded-xl px-3 py-2 text-sm font-medium'
        : 'rounded-2xl px-4 py-3 text-sm font-semibold';

    const state = props.active
        ? props.variant === 'sub'
            ? 'bg-white/10 text-white ring-blue-400/40 shadow-inner'
            : 'bg-gradient-to-r from-blue-500 via-indigo-500 to-slate-900 text-white shadow-lg shadow-blue-500/30 ring-blue-400/50'
        : 'text-blue-100/70 hover:text-white hover:bg-white/10 hover:ring-white/10';

    return [base, ring, variantBase, state].join(' ');
});
</script>

<template>
    <div>
        <button v-if="as === 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
