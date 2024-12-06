<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <!-- Widgets informativos -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Actividades</h2>
                        <p class="text-blue-300 text-2xl">{{ activities.length }}</p>
                    </div>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg flex items-center justify-between">
                    <div>
                        <h2 class="text-lg text-blue-500 font-semibold">Total Notas</h2>
                        <p class="text-blue-300 text-2xl">{{ notes.length }}</p>
                    </div>
                </div>
            </div>

            <!-- Detalles de la Oportunidad -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl text-blue-500 font-bold">Detalles de la Oportunidad</h2>
                    <div class="flex space-x-2">
                        <!-- Botón Editar -->
                        <button
                            class="p-2 rounded-full hover:bg-gray-400"
                            @click="editOpportunity"
                            title="Editar Oportunidad"
                        >
                            <EditIcon class="w-5 "/>
                        </button>
                        <!-- Botón Eliminar -->
                        <button
                            class="p-2 rounded-full hover:bg-gray-400"
                            @click="deleteOpportunity"
                            title="Eliminar Oportunidad"
                        >
                            <DeleteIcon class="w-5"/>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <p><strong>Descripción:</strong> {{ opportunity.description }}</p>
                    <p><strong>Valor:</strong> ${{ opportunity.value }}</p>
                    <p><strong>Probabilidad de Éxito:</strong> {{ opportunity.probability }}%</p>
                    <p><strong>Estado:</strong> {{ opportunity.status }}</p>
                    <template v-for="lead in props.leads">
                        <p v-if="lead.id === opportunity.lead_id"><strong>Lead:</strong> {{ lead.name }}</p>
                    </template>
                </div>
            </div>

            <!-- Actividades -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl text-blue-500 font-bold">Actividades</h2>
                    <button
                        class="bg-blue-500 text-white px-4 py-2 rounded-md flex space-x-5 hover:bg-blue-700"
                        @click="createActivity"
                    >
                        <AddProductIcon class="w-5 fill-gray-50 stroke-gray-50"/>
                        <p> Nueva Actividad
                        </p></button>
                </div>
                <ul v-if="activities.length" class="list-disc pl-6">
                    <li v-for="activity in activities" :key="activity.id" class="mb-4 flex justify-between items-center">
                        <div>
                            <p><strong>{{ activity.type }}</strong></p>
                            <p class="text-gray-600">{{ activity.notes }}</p>
                            <p class="text-sm text-gray-500">Fecha: {{ activity.date }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Botón Editar Actividad -->
                            <button
                                class="p-2 rounded-full hover:bg-gray-400"
                                @click="editActivity(activity)"
                                title="Editar Actividad"
                            >
                                <EditIcon class="w-5"/>
                            </button>
                            <!-- Botón Eliminar Actividad -->
                            <button
                                class="p-2 rounded-full hover:bg-gray-400"
                                @click="deleteActivity(activity)"
                                title="Eliminar Actividad"
                            >
                                <DeleteIcon class="w-5"/>
                            </button>
                        </div>
                    </li>
                </ul>
                <p v-else class="text-gray-500">No hay actividades registradas.</p>
            </div>

            <!-- Notas -->
            <!-- Notas -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl text-blue-500 font-bold">Notas</h2>
                    <button
                        class="bg-blue-500 text-white px-4 py-2 rounded-md flex space-x-5 hover:bg-blue-700"
                        @click="createNote"
                    >
                        <AddProductIcon class="w-5 fill-gray-50 stroke-gray-50"/>
                        <p> Nueva Nota</p>
                    </button>
                </div>
                <ul v-if="notes.length" class="list-disc pl-6">
                    <li v-for="note in notes" :key="note.id" class="mb-4 flex justify-between items-center">
                        <div>
                            <p class="text-gray-600">{{ note.content }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <!-- Botón Editar Nota -->
                            <button
                                class="p-2 rounded-full hover:bg-gray-400"
                                @click="editNote(note)"
                                title="Editar Nota"
                            >
                                <EditIcon class="w-5"/>
                            </button>
                            <!-- Botón Eliminar Nota -->
                            <button
                                class="p-2 rounded-full hover:bg-gray-400"
                                @click="deleteNote(note)"
                                title="Eliminar Nota"
                            >
                                <DeleteIcon class="w-5"/>
                            </button>
                        </div>
                    </li>
                </ul>
                <p v-else class="text-gray-500">No hay notas registradas.</p>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import {ref} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import AddProductIcon from "@/Components/Icons/AddProductIcon.vue";

// Props recibidos
const props = defineProps({
    opportunity: Object,
    activities: Array,
    notes: Array,
    leads: Array,
});

const opportunity = ref(props.opportunity);
const activities = ref(props.activities);
const notes = ref(props.notes);

// Funciones para acciones
const createActivity = () => {
    Inertia.visit(route('activities.goToCreate', {
        lead: opportunity.value.lead_id,
        opportunity: opportunity.value.id
    }));
};

const createNote = () => {
    Inertia.visit(route('notes.goToCreate', {opportunity: opportunity.value.id, lead: opportunity.value.lead_id}));
};

const editOpportunity = () => {
    Inertia.visit(route('opportunities.goToEdit', {id: opportunity.value.id}));
};

const deleteOpportunity = () => {
    if (confirm('¿Estás seguro de que deseas eliminar esta oportunidad?')) {
        Inertia.delete(route('opportunities.destroy', {id: opportunity.value.id}));
    }
};
// Funciones para notas
const editNote = (note) => {
    Inertia.visit(route('notes.goToEdit', {id: note.id}));
};

const deleteNote = (note) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta nota?')) {
        Inertia.delete(route('notes.destroy', {id: note.id}));
    }
};


// Funciones para actividades
const editActivity = (activity) => {
    Inertia.visit(route('activities.goToEdit', {id: activity.id}));
};

const deleteActivity = (activity) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta actividad?')) {
        Inertia.delete(route('activities.destroy', {id: activity.id}));
    }
};
</script>
