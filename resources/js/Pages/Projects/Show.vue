<template>
    <AppLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-6xl mx-auto space-y-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-semibold text-gray-800">{{ project.name }}</h1>
                            <p class="text-sm text-gray-500 mt-1">Estado: <span class="font-medium capitalize">{{ statusLabel(project.status) }}</span></p>
                        </div>
                        <Link :href="route('projects.edit', project.id)" class="px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Editar</Link>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div><strong>Departamento:</strong> {{ project.department?.name ?? '—' }}</div>
                        <div><strong>Cliente:</strong> {{ project.client?.name ?? '—' }}</div>
                        <div><strong>Inicio:</strong> {{ formatDate(project.start_date) }}</div>
                        <div><strong>Fin:</strong> {{ formatDate(project.end_date) }}</div>
                        <div><strong>Presupuesto:</strong> {{ formatCurrency(project.budget) }}</div>
                        <div><strong>Descripción:</strong> {{ project.description || 'Sin descripción' }}</div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Fases</h2>
                        <button @click="addPhaseField" class="px-3 py-1 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">Nueva fase</button>
                    </div>

                    <div v-if="!phaseForms.length" class="text-sm text-gray-500">No hay fases registradas.</div>
                    <div v-for="(phase, index) in phaseForms" :key="phase.id ?? index" class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Nombre</label>
                                <input v-model="phase.name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Estado</label>
                                <select v-model="phase.status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending">Pendiente</option>
                                    <option value="in_progress">En progreso</option>
                                    <option value="completed">Completado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Inicio</label>
                                <input v-model="phase.start_date" type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Fin</label>
                                <input v-model="phase.end_date" type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500">Responsable</label>
                                <select v-model="phase.responsible_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option :value="null">Sin responsable</option>
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500">Notas</label>
                                <textarea v-model="phase.notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end gap-3">
                            <button v-if="phase.id" type="button" class="text-sm text-blue-600 hover:underline" @click="updatePhase(phase)">Guardar</button>
                            <button v-if="phase.id" type="button" class="text-sm text-red-600 hover:underline" @click="deletePhase(phase)">Eliminar</button>
                            <button v-else type="button" class="text-sm text-green-600 hover:underline" @click="storePhase(phase, index)">Crear fase</button>
                            <button v-else type="button" class="text-sm text-gray-500 hover:underline" @click="removePhaseField(index)">Cancelar</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Responsables</h2>
                        <button @click="addResponsibleField" class="px-3 py-1 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200">Nuevo responsable</button>
                    </div>

                    <div v-if="!responsibleForms.length" class="text-sm text-gray-500">No hay responsables asociados.</div>
                    <div v-for="(responsible, index) in responsibleForms" :key="responsible.id ?? index" class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Empleado</label>
                                <select v-model="responsible.employee_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="" disabled>Selecciona un empleado</option>
                                    <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Rol</label>
                                <input v-model="responsible.role" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end gap-3">
                            <button v-if="responsible.id" type="button" class="text-sm text-blue-600 hover:underline" @click="updateResponsible(responsible)">Guardar</button>
                            <button v-if="responsible.id" type="button" class="text-sm text-red-600 hover:underline" @click="deleteResponsible(responsible)">Eliminar</button>
                            <button v-else type="button" class="text-sm text-green-600 hover:underline" @click="storeResponsible(responsible, index)">Asignar</button>
                            <button v-else type="button" class="text-sm text-gray-500 hover:underline" @click="removeResponsibleField(index)">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    project: { type: Object, required: true },
    employees: { type: Array, default: () => [] },
});

const project = props.project;
const employees = props.employees;

const phaseForms = reactive(project.phases.map(phase => ({ ...phase })));
const responsibleForms = reactive(project.responsibles.map(responsible => ({ ...responsible })));

const addPhaseField = () => {
    phaseForms.push({
        id: null,
        name: '',
        status: 'pending',
        start_date: '',
        end_date: '',
        responsible_id: null,
        notes: '',
    });
};

const removePhaseField = (index) => {
    phaseForms.splice(index, 1);
};

const addResponsibleField = () => {
    responsibleForms.push({
        id: null,
        employee_id: '',
        role: '',
    });
};

const removeResponsibleField = (index) => {
    responsibleForms.splice(index, 1);
};

const storePhase = (phase, index) => {
    router.post(route('projects.phases.store', project.id), phase, {
        onSuccess: () => phaseForms.splice(index, 1),
    });
};

const updatePhase = (phase) => {
    router.put(route('projects.phases.update', { project: project.id, phase: phase.id }), phase);
};

const deletePhase = (phase) => {
    if (confirm('¿Eliminar esta fase?')) {
        router.delete(route('projects.phases.destroy', { project: project.id, phase: phase.id }));
    }
};

const storeResponsible = (responsible, index) => {
    router.post(route('projects.responsibles.store', project.id), responsible, {
        onSuccess: () => responsibleForms.splice(index, 1),
    });
};

const updateResponsible = (responsible) => {
    router.put(route('projects.responsibles.update', { project: project.id, responsible: responsible.id }), responsible);
};

const deleteResponsible = (responsible) => {
    if (confirm('¿Eliminar este responsable?')) {
        router.delete(route('projects.responsibles.destroy', { project: project.id, responsible: responsible.id }));
    }
};

const formatDate = (value) => {
    if (!value) {
        return '—';
    }
    return new Date(value).toLocaleDateString();
};

const formatCurrency = (value) => {
    if (!value) {
        return '—';
    }
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const statusLabel = (status) => {
    const normalized = (status || '').toLowerCase();
    switch (normalized) {
        case 'planning':
            return 'Planificación';
        case 'in_progress':
            return 'En progreso';
        case 'completed':
            return 'Completado';
        case 'invoiced':
            return 'Facturado';
        case 'archived':
            return 'Archivado';
        default:
            return status || 'Sin estado';
    }
};
</script>
