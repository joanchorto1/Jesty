<template>
    <AppLayout>
        <div class="container bg-gray-50 w-full min-h-screen mx-auto p-6">
            <h1 class="text-2xl font-bold mb-6">Crear Nuevo Cliente</h1>
            <form @submit.prevent="createClient">

                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Nombre del Cliente</label>
                    <input v-model="form.name" id="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="nif" class="block text-gray-700">NIF</label>
                    <input v-model="form.nif" id="nif" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="bank" class="block text-gray-700">Banco</label>
                    <input v-model="form.bank" id="bank" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700">Teléfono</label>
                    <input v-model="form.phone" id="phone" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input v-model="form.email" id="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-gray-700">Dirección</label>
                    <input v-model="form.address" id="address" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <button type="submit" class="bg-blue-900  hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                    <SaveIcon class="w-5 h-5 stroke-gray-300 "/>
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

const props = defineProps({
    company: Object,
});
const form = reactive({
    company_id:props.company.id,
    name: '',
    nif: '',
    bank: '',
    phone: '',
    email: '',
    address: '',
});

const createClient = async () => {
    try {
        await Inertia.post(route('clients.store'), form);
        Inertia.visit(route('clients')); // Redirige a la lista de clientes después de crear
    } catch (error) {
        console.error('Error al crear el cliente:', error);
    }
};
</script>

<style scoped>
/* Estilos adicionales si es necesario */
</style>
