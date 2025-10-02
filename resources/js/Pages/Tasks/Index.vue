<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 pb-20">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-violet-200 text-sm uppercase tracking-widest">Tareas</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Coordina la ejecución con tu equipo</h1>
                        <p class="text-sm text-violet-200">Distribuye responsabilidades y haz seguimiento de los compromisos derivados de cada oportunidad.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <StatsCard label="Tareas" :value="totalTasks" :hint="`Pendientes ${pendingTasks}`" />
                        <StatsCard label="En progreso" :value="inProgressTasks" :hint="`Completadas ${completedTasks}`" />
                        <StatsCard label="Vencidas" :value="overdueTasks" :hint="'Prioriza acciones críticas'" />
                        <StatsCard label="Asignadas" :value="assignedUsers" :hint="topAssigneeLabel" />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 space-y-10">
                <SectionCard title="Flujo comercial" description="Las tareas consolidan todo el trabajo pendiente para cerrar oportunidades con éxito.">
                    <FlowStepper :steps="flowSteps" :active-index="2" />
                </SectionCard>

                <SectionCard title="Lista de tareas" description="Filtra por estado, prioridad o responsable para asegurar el cumplimiento de compromisos.">
                    <template #actions>
                        <div class="flex flex-wrap items-center gap-3">
                            <select v-model="filters.status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todos los estados</option>
                                <option value="pending">Pendientes</option>
                                <option value="in_progress">En progreso</option>
                                <option value="completed">Completadas</option>
                            </select>
                            <select v-model="filters.priority" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todas las prioridades</option>
                                <option value="low">Baja</option>
                                <option value="medium">Media</option>
                                <option value="high">Alta</option>
                            </select>
                            <input v-model="filters.search" type="text" placeholder="Buscar por título, oportunidad o responsable" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200" />
                            <NavLink :href="route('tasks.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                <span>Nueva tarea</span>
                                <AddIcon class="h-4 w-4 fill-white" />
                            </NavLink>
                        </div>
                    </template>

                    <div v-if="hasError" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-600">
                        {{ errorMessage }}
                    </div>
                    <div v-else-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="i in 4" :key="i" class="animate-pulse rounded-2xl border border-slate-200/60 bg-white/60 p-6">
                            <div class="h-4 w-24 rounded-full bg-slate-200"></div>
                            <div class="mt-4 h-6 w-3/4 rounded-full bg-slate-200"></div>
                            <div class="mt-6 space-y-2">
                                <div class="h-3 w-full rounded-full bg-slate-100"></div>
                                <div class="h-3 w-5/6 rounded-full bg-slate-100"></div>
                                <div class="h-3 w-2/3 rounded-full bg-slate-100"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="filteredTasks.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="task in filteredTasks" :key="task.id" class="rounded-2xl border border-slate-200/70 bg-white p-6 transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-slate-800">{{ task.title }}</h3>
                                        <span :class="['rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-widest', priorityBadge(task.priority)]">
                                            {{ readablePriority(task.priority) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-500">{{ task.description ?? 'Sin descripción registrada.' }}</p>
                                    <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ formatDate(task.due_date) }}
                                        </span>
                                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-9A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21h9a2.25 2.25 0 002.25-2.25V15" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l3 3L21 6" />
                                            </svg>
                                            Estado: {{ readableStatus(task.status) }}
                                        </span>
                                        <span v-if="task.assigned_to" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a8.25 8.25 0 0115 0" />
                                            </svg>
                                            {{ assigneeName(task.assigned_to) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                    <div class="text-xs uppercase tracking-widest text-slate-400">
                                        {{ relatedOpportunity(task.opportunity) }}
                                    </div>
                                    <div class="flex gap-2">
                                        <NavLink :href="route('tasks.show', task.id)" class="rounded-full border border-violet-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-violet-600 transition hover:bg-violet-600 hover:text-white">Ver</NavLink>
                                        <NavLink :href="route('tasks.edit', task.id)" class="rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">Editar</NavLink>
                                        <button @click="deleteTask(task.id)" class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="No hay tareas asignadas" description="Define acciones concretas para cada oportunidad y haz seguimiento al progreso.">
                            <template #action>
                                <NavLink :href="route('tasks.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                    <AddIcon class="h-4 w-4 fill-white" />
                                    Crear tarea
                                </NavLink>
                            </template>
                        </EmptyState>
                    </div>
                </SectionCard>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import StatsCard from '@/Components/Crm/StatsCard.vue';
import SectionCard from '@/Components/Crm/SectionCard.vue';
import FlowStepper from '@/Components/Crm/FlowStepper.vue';
import EmptyState from '@/Components/Crm/EmptyState.vue';

const props = defineProps({
    tasks: {
        type: [Array, Object],
        default: () => ([]),
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: [String, Object],
        default: null,
    },
});

const tasks = computed(() => {
    if (Array.isArray(props.tasks)) return props.tasks;
    return props.tasks?.data ?? [];
});

const filters = reactive({
    status: 'all',
    priority: 'all',
    search: '',
});

const totalTasks = computed(() => tasks.value.length);
const pendingTasks = computed(() => tasks.value.filter(task => ['pending', 'pendiente'].includes((task.status || '').toLowerCase())).length);
const inProgressTasks = computed(() => tasks.value.filter(task => ['in_progress', 'en progreso', 'en_progreso'].includes((task.status || '').toLowerCase())).length);
const completedTasks = computed(() => tasks.value.filter(task => ['completed', 'completada', 'hecha'].includes((task.status || '').toLowerCase())).length);
const overdueTasks = computed(() => tasks.value.filter(task => {
    if (!task.due_date) return false;
    const due = new Date(task.due_date);
    return due < new Date() && !['completed', 'completada', 'hecha'].includes((task.status || '').toLowerCase());
}).length);
const assignedUsers = computed(() => {
    const set = new Set(tasks.value.map(task => task.assigned_to?.id ?? task.assigned_to).filter(Boolean));
    return set.size || '—';
});
const topAssigneeLabel = computed(() => {
    if (!tasks.value.length) return 'Sin responsables destacados';
    const counts = tasks.value.reduce((acc, task) => {
        const name = task.assigned_to?.name ?? task.assigned_to?.full_name ?? (typeof task.assigned_to === 'string' ? task.assigned_to : 'Sin asignar');
        acc[name] = (acc[name] || 0) + 1;
        return acc;
    }, {});
    const [name] = Object.entries(counts).sort((a, b) => b[1] - a[1])[0] || [];
    return name ? `Responsable destacado: ${name}` : 'Sin responsables destacados';
});

const filteredTasks = computed(() => {
    return tasks.value.filter(task => {
        const status = (task.status || '').toLowerCase();
        const priority = (task.priority || '').toLowerCase();
        const statusMatch = (() => {
            if (filters.status === 'all') return true;
            if (filters.status === 'pending') return ['pending', 'pendiente'].includes(status);
            if (filters.status === 'in_progress') return ['in_progress', 'en progreso', 'en_progreso'].includes(status);
            if (filters.status === 'completed') return ['completed', 'completada', 'hecha'].includes(status);
            return true;
        })();
        const priorityMatch = filters.priority === 'all' ? true : priority === filters.priority;
        const searchSpace = [task.title, task.description, relatedOpportunity(task.opportunity), assigneeName(task.assigned_to)].join(' ').toLowerCase();
        const searchMatch = filters.search ? searchSpace.includes(filters.search.toLowerCase()) : true;
        return statusMatch && priorityMatch && searchMatch;
    });
});

const flowSteps = computed(() => ([
    {
        label: 'Lead',
        description: 'Información calificada y perfilado',
    },
    {
        label: 'Oportunidad',
        description: 'Seguimiento comercial activo',
    },
    {
        label: 'Tareas',
        description: 'Ejecución coordinada para cerrar',
    },
]));

const isLoading = computed(() => props.isLoading);
const hasError = computed(() => Boolean(props.error));
const errorMessage = computed(() => (typeof props.error === 'string' ? props.error : props.error?.message ?? 'No se pudo cargar la información.'));

const deleteTask = id => {
    if (confirm('¿Estás seguro de eliminar esta tarea?')) {
        Inertia.delete(route('tasks.destroy', id));
    }
};

const readableStatus = status => {
    const value = (status || '').toLowerCase();
    if (['completed', 'completada', 'hecha'].includes(value)) return 'Completada';
    if (['in_progress', 'en progreso', 'en_progreso'].includes(value)) return 'En progreso';
    if (['pending', 'pendiente'].includes(value)) return 'Pendiente';
    return status ?? 'Sin estado';
};

const readablePriority = priority => {
    const value = (priority || '').toLowerCase();
    if (value === 'high') return 'Alta';
    if (value === 'medium') return 'Media';
    if (value === 'low') return 'Baja';
    return priority ?? 'Sin prioridad';
};

const priorityBadge = priority => {
    const value = (priority || '').toLowerCase();
    if (value === 'high') return 'bg-rose-100 text-rose-600';
    if (value === 'medium') return 'bg-amber-100 text-amber-600';
    if (value === 'low') return 'bg-emerald-100 text-emerald-600';
    return 'bg-slate-100 text-slate-500';
};

const formatDate = date => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString();
};

const assigneeName = assignee => {
    if (!assignee) return 'Sin asignar';
    if (typeof assignee === 'string') return assignee;
    return assignee.name ?? assignee.full_name ?? 'Sin asignar';
};

const relatedOpportunity = opportunity => {
    if (!opportunity) return 'Sin oportunidad relacionada';
    if (typeof opportunity === 'string') return `Oportunidad: ${opportunity}`;
    if (typeof opportunity === 'object') {
        return `Oportunidad: ${opportunity.description ?? opportunity.title ?? opportunity.name ?? opportunity.id}`;
    }
    return 'Sin oportunidad relacionada';
};
</script>
