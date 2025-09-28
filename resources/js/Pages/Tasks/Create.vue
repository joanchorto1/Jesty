<template>
    <AppLayout>
        <div class="p-6 bg-gray-100 min-h-screen">
            <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
                <h1 class="text-2xl font-semibold mb-6 text-gray-800">Crear Tarea CRM</h1>
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="title">Título</label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="status">Estado</label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="pending">Pendiente</option>
                                <option value="in_progress">En progreso</option>
                                <option value="completed">Completada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="due_date">Fecha límite</label>
                            <input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="project_id">Proyecto</label>
                            <select
                                id="project_id"
                                v-model="form.project_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option :value="null">Sin proyecto</option>
                                <option v-for="project in projects" :key="project.id" :value="project.id">
                                    {{ project.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="lead_id">Lead asociado</label>
                            <select
                                id="lead_id"
                                v-model="form.lead_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option :value="null">Sin lead</option>
                                <option v-for="lead in leads" :key="lead.id" :value="lead.id">
                                    {{ lead.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="opportunity_id">Oportunidad</label>
                            <select
                                id="opportunity_id"
                                v-model="form.opportunity_id"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option :value="null">Sin oportunidad</option>
                                <option v-for="opportunity in opportunities" :key="opportunity.id" :value="opportunity.id">
                                    {{ opportunity.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700" for="description">Descripción</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <Link :href="route('tasks.index')" class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700"
                            :disabled="form.processing"
                        >
                            Guardar tarea
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
    leads: { type: Array, default: () => [] },
    opportunities: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
});

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    status: 'pending',
    lead_id: null,
    opportunity_id: null,
    project_id: null,
});

const submit = () => {
    form.post(route('tasks.store'));
};
</script>
