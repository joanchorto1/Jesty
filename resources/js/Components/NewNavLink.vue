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
        ? 'rounded-lg px-3 py-2 text-sm font-medium'
        : 'rounded-lg px-4 py-3 text-sm font-medium';

    const state = props.active
        ? props.variant === 'sub'
            ? 'bg-slate-900 text-white ring-slate-900'
            : 'bg-slate-900 text-white ring-slate-900'
        : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100 hover:ring-slate-200';

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
