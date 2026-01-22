<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
        ? 'block w-full border-l border-slate-900 bg-slate-50 px-3 py-2 text-start text-sm font-medium text-slate-900 transition duration-150 ease-in-out'
        : 'block w-full border-l border-transparent px-3 py-2 text-start text-sm font-medium text-slate-500 transition duration-150 ease-in-out hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:border-slate-300 focus:bg-slate-50 focus:text-slate-900';
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
