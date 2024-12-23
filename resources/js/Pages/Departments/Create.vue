<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl text-blue-500 font-semibold mb-6">Crear Departamento</h2>

                <form @submit.prevent="submitForm">
                    <!-- Nombre del Departamento -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" v-model="form.name" id="name" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Gerente -->
                    <div class="mb-4">
                        <label for="manager_id" class="block text-sm font-medium text-gray-700">Gerente</label>
                        <select v-model="form.manager_id" id="manager_id" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="mb-4">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Crear Departamento</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    employees: Array,
});

const form = ref({
    name: '',
    manager_id: ''
});

const submitForm = () => {
Inertia.post(route('departments.store'), form.value);
};
</script>
