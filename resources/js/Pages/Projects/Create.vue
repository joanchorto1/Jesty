<template>
    <AppLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">Crear proyecto</h1>
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

                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-lg font-semibold text-gray-800">Fases</h2>
                            <button type="button" class="px-3 py-1 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200" @click="addPhase">Añadir fase</button>
                        </div>
                        <div v-if="!form.phases.length" class="text-sm text-gray-500 mb-4">No se han definido fases iniciales.</div>
                        <div v-for="(phase, index) in form.phases" :key="index" class="mb-4 border border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input v-model="phase.name" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                                    <select v-model="phase.status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="pending">Pendiente</option>
                                        <option value="in_progress">En progreso</option>
                                        <option value="completed">Completado</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Inicio</label>
                                    <input v-model="phase.start_date" type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fin</label>
                                    <input v-model="phase.end_date" type="date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Responsable</label>
                                    <select v-model="phase.responsible_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option :value="null">Sin responsable</option>
                                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Notas</label>
                                    <textarea v-model="phase.notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                            <div class="mt-3 text-right">
                                <button type="button" class="text-sm text-red-600 hover:underline" @click="removePhase(index)">Eliminar fase</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-lg font-semibold text-gray-800">Responsables del proyecto</h2>
                            <button type="button" class="px-3 py-1 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200" @click="addResponsible">Añadir responsable</button>
                        </div>
                        <div v-if="!form.responsibles.length" class="text-sm text-gray-500 mb-4">No hay responsables asignados.</div>
                        <div v-for="(responsible, index) in form.responsibles" :key="index" class="mb-4 border border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Empleado</label>
                                    <select v-model="responsible.employee_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="" disabled>Selecciona un empleado</option>
                                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                                    <input v-model="responsible.role" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Rol dentro del proyecto" />
                                </div>
                            </div>
                            <div class="mt-3 text-right">
                                <button type="button" class="text-sm text-red-600 hover:underline" @click="removeResponsible(index)">Eliminar responsable</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <Link :href="route('projects.index')" class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">Cancelar</Link>
                        <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700" :disabled="form.processing">
                            Guardar proyecto
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
    departments: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    employees: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    status: 'planning',
    department_id: '',
    client_id: null,
    start_date: '',
    end_date: '',
    budget: '',
    description: '',
    phases: [],
    responsibles: [],
});

const addPhase = () => {
    form.phases.push({
        name: '',
        status: 'pending',
        start_date: '',
        end_date: '',
        responsible_id: null,
        notes: '',
    });
};

const removePhase = (index) => {
    form.phases.splice(index, 1);
};

const addResponsible = () => {
    form.responsibles.push({
        employee_id: '',
        role: '',
    });
};

const removeResponsible = (index) => {
    form.responsibles.splice(index, 1);
};

const submit = () => {
    form.post(route('projects.store'));
};
</script>
