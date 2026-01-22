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
        ? 'block w-full rounded-lg bg-slate-100 px-3 py-2 text-start text-sm font-medium text-slate-900 transition duration-150 ease-in-out'
        : 'block w-full rounded-lg px-3 py-2 text-start text-sm font-medium text-slate-500 transition duration-150 ease-in-out hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:bg-slate-100 focus:text-slate-900';
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
