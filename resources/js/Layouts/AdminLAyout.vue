<template>
    <div class="flex h-screen bg-gray-50">

        <!-- Sidebar -->
        <aside class="w-64  text-white bg-blue-500 flex-col">
            <div class="p-4">
                <h1 class="text-2xl font-bold"><a :href="route('admin.dashboard')"> Admin Panel</a></h1>
            </div>

            <nav class="mt-10 ">
                <ul>
                    <li class="mb-4">
                        <router-link  class="flex items-center p-3 hover:bg-blue-200 rounded-lg">
                            <span class="ml-2">Compañías</span>
                        </router-link>
                    </li>
                    <li class="mb-4">
                        <router-link to="/admin/users" class="flex items-center p-3 hover:bg-blue-200 rounded-lg">
                            <span class="ml-2">Usuarios</span>
                        </router-link>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 w-full">

            <!-- Top Bar -->
            <header class="bg-white shadow-md p-4 flex justify-between items-center">
                <!-- Logo -->
                <div class="flex space-x-5 items-center">
                    <img src="/storage/POPLogo.jpeg" alt="Logo" class="h-8 w-8 mr-10 rounded-md ">
                    <span class="text-xl font-semibold">Admin Dashboard</span>
                </div>

                <!-- Logout Button -->
                <div>
                    <button
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg"
                        @click="logout"
                    >
                        Logout
                    </button>
                </div>
            </header>

            <!-- Slot for Content -->
            <main class="p-6 flex-1">
                <slot></slot>
            </main>

        </div>
    </div>
</template>

<script setup>
import {Inertia} from "@inertiajs/inertia";

const logout = () => {
Inertia.post(route('admin.logout'))
};
</script>

<style scoped>
/* Custom styles */
</style>
