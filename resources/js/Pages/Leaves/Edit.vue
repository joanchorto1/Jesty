<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl text-blue-500 font-semibold mb-6">Editar Licencia</h2>

                <form @submit.prevent="submitForm">
                    <!-- Employee ID -->
                    <div class="mb-4">
                        <label for="employee_id" class="block text-sm font-medium text-gray-700">Empleado</label>
                        <select v-model="form.employee_id" id="employee_id" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                        </select>
                    </div>

                    <!-- Tipo de Licencia -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700">Tipo de Licencia</label>
                        <select v-model="form.type" id="type" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option value="sick">Enfermedad</option>
                            <option value="vacation">Vacaciones</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>

                    <!-- Fecha de Inicio -->
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                        <input type="date" v-model="form.start_date" id="start_date" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Fecha de Fin -->
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                        <input type="date" v-model="form.end_date" id="end_date" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <!-- Razón -->
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700">Razón</label>
                        <textarea v-model="form.reason" id="reason" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <!-- Estado -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select v-model="form.status" id="status" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option value="aprobado">Aprobado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="denegado">Denegado</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="mb-4">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Actualizar Licencia</button>
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
    employees: Array,
    leave: Object
});
console.log(props.leave);

const form = ref({
    employee_id: props.leave.employee_id,
    type: props.leave.type,
    start_date: props.leave.start_date,
    end_date: props.leave.end_date,
    reason: props.leave.reason,
    status: props.leave.status
});

const submitForm = () => {
    Inertia.put(route('leaves.update2', props.leave.id), form.value);
    console.log(form.value);
};
</script>
