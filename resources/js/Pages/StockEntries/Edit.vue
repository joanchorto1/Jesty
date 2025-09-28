<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 flex items-center justify-center p-6">
            <div class="max-w-xl w-full bg-white shadow-md rounded-lg p-8 text-center space-y-4">
                <h1 class="text-2xl font-semibold text-blue-700">Editar servei</h1>
                <p class="text-gray-600">
                    Per modificar un servei, utilitza la pantalla d'edició corresponent del catàleg. Aquí ja no cal ajustar quantitats d'inventari.
                </p>
                <NavLink :href="route('products.index')" class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-md">
                    Tornar als serveis
                </NavLink>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
</script>
