<template>
    <AppLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Editar proyecto</h1>
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="name">Nombre</label>
                            <input v-model="form.name" id="name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="status">Estado</label>
                            <select v-model="form.status" id="status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="planning">Planificación</option>
                                <option value="in_progress">En progreso</option>
                                <option value="completed">Completado</option>
                                <option value="invoiced">Facturado</option>
                                <option value="archived">Archivado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="department_id">Departamento</label>
                            <select v-model="form.department_id" id="department_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="" disabled>Selecciona un departamento</option>
                                <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="client_id">Cliente</label>
                            <select v-model="form.client_id" id="client_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option :value="null">Sin cliente</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="start_date">Fecha inicio</label>
                            <input v-model="form.start_date" id="start_date" type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="end_date">Fecha fin</label>
                            <input v-model="form.end_date" id="end_date" type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="budget">Presupuesto (€)</label>
                            <input v-model="form.budget" id="budget" type="number" min="0" step="0.01" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700" for="description">Descripción</label>
                            <textarea v-model="form.description" id="description" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p>Este proyecto tiene <strong>{{ project.phases.length }}</strong> fases y <strong>{{ project.responsibles.length }}</strong> responsables definidos.</p>
                        <p class="mt-1">Puedes gestionarlos en la vista detallada del proyecto.</p>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <Link :href="route('projects.show', project.id)" class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">Volver</Link>
                        <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700" :disabled="form.processing">
                            Actualizar proyecto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: { type: Object, required: true },
    departments: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
});

const form = useForm({
    name: props.project.name,
    status: props.project.status || 'planning',
    department_id: props.project.department_id,
    client_id: props.project.client_id,
    start_date: props.project.start_date,
    end_date: props.project.end_date,
    budget: props.project.budget,
    description: props.project.description,
});

const submit = () => {
    form.put(route('projects.update', props.project.id));
};
</script>
