<template>
    <AppLayout>
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-6">Editar Cliente</h1>
            <form @submit.prevent="updateClient">

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
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar Cliente
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    client: Object,
});

const form = reactive({
    company_id: props.client.company_id || '',
    name: props.client.name || '',
    nif: props.client.nif || '',
    bank: props.client.bank || '',
    phone: props.client.phone || '',
    email: props.client.email || '',
    address: props.client.address || '',
});

onMounted(() => {
    if (!props.client) {
        // Manejar el caso en el que el cliente no se pasa como prop (por ejemplo, cuando no se ha cargado)
        console.error('Cliente no proporcionado.');
    }
});

const updateClient = async () => {
    try {
        await Inertia.put(route('clients.update', props.client.id), form);
        Inertia.visit(route('clients')); // Redirige a la lista de clientes después de actualizar
    } catch (error) {
        console.error('Error al actualizar el cliente:', error);
    }
};
</script>

<style scoped>
/* Estilos adicionales si es necesario */
</style>
