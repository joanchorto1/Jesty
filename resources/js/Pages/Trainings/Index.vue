<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Entrenamientos</h2>
                        <p class="text-blue-300 text-2xl">{{ totalTrainings }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabla de Entrenamientos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <NavLink :href="route('trainings.create')" class="bg-blue-500 hover:text-gray-50 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                    Agregar nuevo Entrenamiento
                    <AddIcon class="w-6 h-6 fill-gray-200 ml-3"/>
                </NavLink>
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Entrenamiento</th>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Duración</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="training in filteredTrainings" :key="training.id" class="border-t">
                        <td class="px-4 py-2">{{ training.name }}</td>
                        <td class="px-4 py-2">{{ training.date }}</td>
                        <td class="px-4 py-2">{{ training.duration }}</td>
                        <td class="px-4 py-2">
                            <NavLink :href="route('trainings.show', training.id)" class="text-blue-500">
                                <InfoIcon class="w-5 h-5"/>
                            </NavLink>
                            <NavLink :href="route('trainings.edit', training.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteTraining(training.id)" class="text-red-500 ml-2">
                                <DeleteIcon class="w-5 h-5"/>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";
import { computed, reactive } from 'vue';
import InfoIcon from "@/Components/Icons/InfoIcon.vue";

const props = defineProps({
    trainings: Array,
});

const filteredTrainings = reactive(props.trainings);
const totalTrainings = computed(() => filteredTrainings.length);

const deleteTraining = (trainingId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este entrenamiento?")) {
        Inertia.delete(route('trainings.destroy', trainingId));
    }
};
</script>
