<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
            </button>
            <span v-else class="inline-flex rounded-md">
                <button type="button" class="inline-flex items-center bg-blue-500 text-gray-50 rounded-2xl  px-3 py-2 border border-transparent text-sm leading-4 font-medium hover:text-gray-200 focus:outline-none focus:bg-blue-700 focus:text-gray-200 active:bg-gray-50 transition ease-in-out duration-150">
                    {{ $page.props.auth.user.name }}
                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </span>
        </template>
        <template #content>
            <div class="block px-4 py-2 text-xs text-gray-600">Manage Account</div>
            <DropdownLink :href="route('profile.show')">Profile</DropdownLink>
            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">API Tokens</DropdownLink>
            <div class="border-t border-gray-200" />
            <form @submit.prevent="logout">
                <DropdownLink as="button">Log Out</DropdownLink>
            </form>
        </template>
    </Dropdown>
</template>

<script>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Inertia } from '@inertiajs/inertia';

export default {
    components: { Dropdown, DropdownLink },
    methods: {
        logout() {
            Inertia.post(route('logout'));
        },
    },
};
</script>
