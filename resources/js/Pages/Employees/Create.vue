<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl text-blue-500 font-semibold mb-6">Crear Empleado</h2>

                <form @submit.prevent="submitForm">
                    <!-- Nombre -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" v-model="form.name" id="name" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required/>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" v-model="form.email" id="email" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required/>
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" v-model="form.phone" id="phone" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required/>
                    </div>

                    <!-- Dirección -->
                    <div class="mb-4">
                        <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" v-model="form.address" id="address" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Cargo -->
                    <div class="mb-4">
                        <label for="position" class="block text-sm font-medium text-gray-700">Cargo</label>
                        <input type="text" v-model="form.job_title" id="position" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required/>
                    </div>

                    <!-- Departamento -->
                    <div class="mb-4">
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Departamento</label>
                        <select v-model="form.department_id" id="department_id" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                        </select>
                    </div>

                    <!-- Salario -->
                    <div class="mb-4">
                        <label for="salary" class="block text-sm font-medium text-gray-700">Salario</label>
                        <input type="number" v-model="form.salary" id="salary" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required/>
                    </div>

                    <!-- Fecha de Contratación -->
                    <div class="mb-4">
                        <label for="hire_date" class="block text-sm font-medium text-gray-700">Fecha de Contratación</label>
                        <input type="date" v-model="form.hire_date" id="hire_date" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required/>
                    </div>

                    <!-- Estado -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select v-model="form.status" id="status" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <!-- DNI -->
                    <div class="mb-4">
                        <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                        <input type="text" v-model="form.dni" id="dni" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Número de Seguridad Social (NNSS) -->
                    <div class="mb-4">
                        <label for="nnss" class="block text-sm font-medium text-gray-700">Número de Seguridad Social</label>
                        <input type="text" v-model="form.nnss" id="nnss" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- IBAN -->
                    <div class="mb-4">
                        <label for="iban" class="block text-sm font-medium text-gray-700">IBAN</label>
                        <input type="text" v-model="form.iban" id="iban" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="mb-4">
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                        <input type="date" v-model="form.birth_date" id="birth_date" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Submit -->
                    <div class="mb-4">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Crear Empleado</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    departments: Array,
});

const form = ref({
    name: '',
    email: '',
    phone: '',
    address: '',
    job_title: '',
    department_id: '',
    salary: '',
    hire_date: '',
    status: 'activo',
    dni: '',
    nnss: '',
    iban: '',
    birth_date: '',
});

const submitForm = () => {
    Inertia.post(route('employees.store'), form.value);
};
</script>

<style scoped>
/* Estilos personalizados */
</style>
