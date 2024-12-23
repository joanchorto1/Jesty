<template>
    <AppLayout>
        <div class="bg-gray-50 min-h-screen py-10 px-6">
            <div class="w-5/6 mx-auto bg-white shadow-md rounded-lg p-8">
                <!-- Título -->
                <h1 class="text-2xl font-bold text-blue-500 mb-8">Detalles de la Formación</h1>

                <!-- Información de la formación -->
                <div class="space-y-6">
                    <p class="text-lg">
                        <strong>Nombre de la Formación:</strong> {{ training.name }}
                    </p>
                    <p class="text-lg">
                        <strong>Descripción:</strong> {{ training.description }}
                    </p>
                    <p class="text-lg">
                        <strong>Fecha:</strong> {{ training.date }}
                    </p>
                    <p class="text-lg">
                        <strong>Duración:</strong> {{ training.duration }} horas
                    </p>

                </div>

                <!-- Lista de empleados -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-blue-500 mb-4">Empleados Participantes</h2>
                    <div v-if="training.employees.length > 0" class="bg-gray-100 rounded-lg p-6">
                        <ul class="divide-y divide-gray-200">
                            <li v-for="employee in training.employees" :key="employee.id" class="py-4 flex items-center justify-between">
                                <div>
                                    <p class="text-lg font-semibold">{{ employee.name }}</p>
                                    <p class="text-gray-600 text-sm">Email: {{ employee.email }}</p>
                                    <p class="text-gray-600 text-sm">Teléfono: {{ employee.phone }}</p>
                                </div>
                                <NavLink :href="route('employees.show', employee.id)" title="Ver Detalles">
                                    <InfoIcon class="w-6 h-6 text-blue-600 hover:text-blue-800" />
                                </NavLink>
                            </li>
                        </ul>
                    </div>
                    <p v-else class="text-gray-500">No hay empleados registrados en esta formación.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";

// Props
const props = defineProps({
    training: Object,
});
</script>

<style scoped>
/* Estilos personalizados */
.text-lg {
    line-height: 1.75rem;
}
</style>
