<template>
    <AdminLAyout>
    <div class="p-6 bg-gray-100 min-h-screen">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Crear Compañía</h1>

        <form @submit.prevent="createCompany" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                    type="text"
                    id="name"
                    v-model="data.form.name"
                    class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    required
                />
            </div>

            <div class="mb-4">
                <label for="nif" class="block text-sm font-medium text-gray-700">NIF</label>
                <input
                    type="text"
                    id="nif"
                    v-model="data.form.nif"
                    class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    required
                />
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input
                    type="text"
                    id="phone"
                    v-model="data.form.phone"
                    class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    required
                />
            </div>

            <div class="mb-4">
                <label for="mail" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                <input
                    type="email"
                    id="mail"
                    v-model="data.form.email"
                    class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    required
                />
            </div>
            <div class="mb-4">
                <label for="mail" class="block text-sm font-medium text-gray-700">Direccion de facturación</label>
                <input
                    type="text"
                    id="address"
                    v-model="data.form.address"
                    class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    required
                />
            </div>

            <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg"
            >
                Crear Compañía
            </button>
        </form>
    </div>
    </AdminLAyout>
</template>

<script setup>
import {reactive, ref} from 'vue'
import AdminLAyout from "@/Layouts/AdminLAyout.vue";
import {Inertia} from "@inertiajs/inertia";

const data = reactive({
    form:{
        name: '',
        nif: '',
        phone: '',
        email: '',
        address:''
    }

})

const createCompany = () => {
    Inertia.post(route('companies.store',data.form))
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
