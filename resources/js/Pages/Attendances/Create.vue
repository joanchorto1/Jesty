<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl text-blue-500 font-semibold mb-6">Registrar Asistencia</h2>

                <form @submit.prevent="submitForm">
                    <!-- Empleado -->
                    <div class="mb-4">
                        <label for="employee_id" class="block text-sm font-medium text-gray-700">Empleado</label>
                        <select v-model="form.employee_id" id="employee_id" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                        </select>
                    </div>

                    <!-- Fecha -->
                    <div class="mb-4">
                        <label for="attendance_date" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" v-model="form.date" id="attendance_date" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Hora de Entrada -->
                    <div class="mb-4">
                        <label for="check_in" class="block text-sm font-medium text-gray-700">Hora de Entrada</label>
                        <input type="time" v-model="form.check_in" id="check_in" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Hora de Salida -->
                    <div class="mb-4">
                        <label for="check_out" class="block text-sm font-medium text-gray-700">Hora de Salida</label>
                        <input type="time" v-model="form.check_out" id="check_out" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Total de Horas -->
                    <div class="mb-4">
                        <label for="total_hours" class="block text-sm font-medium text-gray-700">Total de Horas</label>
                        <input type="text" :value="totalHours" id="total_hours" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" readonly/>
                    </div>

                    <!-- Submit -->
                    <div class="mb-4">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Registrar Asistencia</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    employees: Array,
});

const form = ref({
    employee_id: '',
    date: '',
    check_in: '',
    check_out: ''
});

// Calcular el total de horas dinámicamente
const totalHours = computed(() => {
    if (form.value.check_in && form.value.check_out) {
        const checkIn = new Date(`2021-01-01T${form.value.check_in}`);
        const checkOut = new Date(`2021-01-01T${form.value.check_out}`);
        const diff = checkOut - checkIn;

        if (diff > 0) {
            return (diff / 1000 / 60 / 60).toFixed(2); // Convertir a horas con 2 decimales
        }
    }
    return "0.00"; // Valor por defecto si no hay datos válidos
});

const submitForm = () => {
    const payload = {
        ...form.value,
        total_hours: totalHours.value // Incluir el total de horas calculado
    };

    Inertia.post(route('attendances.store'), payload);
};
</script>
