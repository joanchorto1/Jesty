<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl text-blue-500 font-semibold mb-6">Crear Capacitación</h2>

                <form @submit.prevent="submitForm">
                    <!-- Nombre -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input
                            type="text"
                            v-model="form.name"
                            id="name"
                            class="w-full p-2 mt-2 border border-gray-300 rounded-lg"
                        />
                        <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea
                            v-model="form.description"
                            id="description"
                            class="w-full p-2 mt-2 border border-gray-300 rounded-lg"
                        ></textarea>
                        <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                    </div>

                    <!-- Fecha -->
                    <div class="mb-4">
                        <label for="date" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input
                            type="date"
                            v-model="form.date"
                            id="date"
                            class="w-full p-2 mt-2 border border-gray-300 rounded-lg"
                        />
                        <span v-if="errors.date" class="text-red-500 text-sm">{{ errors.date }}</span>
                    </div>

                    <!-- Duración -->
                    <div class="mb-4">
                        <label for="duration" class="block text-sm font-medium text-gray-700">Duración (Horas)</label>
                        <input
                            type="number"
                            v-model="form.duration"
                            id="duration"
                            class="w-full p-2 mt-2 border border-gray-300 rounded-lg"
                        />
                        <span v-if="errors.duration" class="text-red-500 text-sm">{{ errors.duration }}</span>
                    </div>

                    <!-- Empleados -->
                    <div class="mb-4">
                        <label for="employee_ids" class="block text-sm font-medium text-gray-700">Seleccionar Empleados</label>
                        <select
                            v-model="form.employee_ids"
                            id="employee_ids"
                            class="w-full p-2 mt-2 border border-gray-300 rounded-lg"
                            multiple
                        >
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                {{ employee.name }}
                            </option>
                        </select>
                        <span v-if="errors.employee_ids" class="text-red-500 text-sm">{{ errors.employee_ids }}</span>
                    </div>

                    <!-- Submit -->
                    <div class="mb-4">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Crear Capacitación</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Inertia } from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout.vue";

const form = ref({
    name: '',
    description: '',
    date: '',
    duration: '',
    employee_ids: []
});

const props = defineProps({
    employees: Array
});

const errors = ref({});

const submitForm = () => {
    Inertia.post(route('trainings.store'), form.value, {
        onError: (err) => {
            errors.value = err;
        },
        onSuccess: () => {
            errors.value = {};
        }
    });
};
</script>
