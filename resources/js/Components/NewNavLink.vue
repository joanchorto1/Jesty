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
        ? 'block w-full ps-3 pe-4 py-2  text-start text-base font-medium text-blue-500 bg-blue-50 border-r-4 border-blue-500 focus:outline-none  focus:text-text-gray-950 focus:bg-withe focus:border-blue-700 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2  text-start text-base font-medium text-gray-700 hover:border-r-4 hover:text-gray-950 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
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
