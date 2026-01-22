<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center border-b border-slate-900 px-1 pt-1 text-sm font-medium leading-5 text-slate-900 transition duration-150 ease-in-out'
        : 'inline-flex items-center border-b border-transparent px-1 pt-1 text-sm font-medium leading-5 text-slate-500 transition duration-150 ease-in-out hover:text-slate-900 hover:border-slate-300 focus:outline-none focus:text-slate-900 focus:border-slate-300';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
