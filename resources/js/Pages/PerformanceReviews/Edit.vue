<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl text-blue-500 font-semibold mb-6">Editar Evaluación de Desempeño</h2>

                <form @submit.prevent="submitForm">
                    <!-- Employee ID -->
                    <div class="mb-4">
                        <label for="employee_id" class="block text-sm font-medium text-gray-700">Empleado</label>
                        <select v-model="form.employee_id" id="employee_id" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                        </select>
                    </div>

                    <!-- Fecha de Revisión -->
                    <div class="mb-4">
                        <label for="review_date" class="block text-sm font-medium text-gray-700">Fecha de Revisión</label>
                        <input type="date" v-model="form.review_date" id="review_date" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" />
                    </div>

                    <!-- Calificación -->
                    <div class="mb-4">
                        <label for="score" class="block text-sm font-medium text-gray-700">Calificación</label>
                        <input type="number" v-model="form.score" id="score" class="w-full p-2 mt-2 border border-gray-300 rounded-lg" min="1" max="5" />
                    </div>

                    <!-- Comentarios -->
                    <div class="mb-4">
                        <label for="comments" class="block text-sm font-medium text-gray-700">Comentarios</label>
                        <textarea v-model="form.comments" id="comments" class="w-full p-2 mt-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <!-- Evaluador -->
                    <div class="mb-4">
                        <label for="reviewed_by" class="block text-sm font-medium text-gray-700">Evaluador</label>
                        <select v-model="form.reviewed_by" id="reviewed_by" class="w-full p-2 mt-2 border border-gray-300 rounded-lg">
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="mb-4">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Guardar Cambios</button>
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
    review: Object,
});

const form = ref({
    employee_id: props.review.employee_id,
    review_date: props.review.review_date,
    score: props.review.score,
    comments: props.review.comments,
    reviewed_by: props.review.reviewed_by,
});

const submitForm = () => {
    Inertia.put(route('performance-reviews.update2', props.review.id), form.value);
};
</script>
