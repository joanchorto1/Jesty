<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 pb-20">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-violet-200 text-sm uppercase tracking-widest">Actividades</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Organiza cada interacción comercial</h1>
                        <p class="text-sm text-violet-200">Coordina reuniones, llamadas y seguimientos para mantener el impulso de cada oportunidad.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <StatsCard label="Total" :value="totalActivities" :hint="`Programadas ${upcomingActivities}`" />
                        <StatsCard label="Completadas" :value="completedActivities" :hint="`Pendientes ${pendingActivities}`" />
                        <StatsCard label="Tipos" :value="uniqueTypes" :hint="mostCommonType" />
                        <StatsCard label="Última actividad" :value="latestActivityDate" :hint="'Mantén el ritmo de tu pipeline'" />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 space-y-10">
                <SectionCard title="Flujo comercial" description="Las actividades conectan oportunidades con tareas ejecutables y aseguran el progreso constante.">
                    <FlowStepper :steps="flowSteps" :active-index="2" />
                </SectionCard>

                <SectionCard title="Cronología de actividades" description="Filtra por tipo o estado y visualiza las interacciones recientes y futuras." >
                    <template #actions>
                        <div class="flex flex-wrap items-center gap-3">
                            <select v-model="filters.type" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todos los tipos</option>
                                <option v-for="type in activityTypes" :key="type" :value="type">{{ type }}</option>
                            </select>
                            <select v-model="filters.state" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todos los estados</option>
                                <option value="pending">Pendientes</option>
                                <option value="completed">Completadas</option>
                                <option value="overdue">Vencidas</option>
                            </select>
                            <input v-model="filters.search" type="text" placeholder="Buscar por título o descripción" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200" />
                            <NavLink :href="route('activities.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                <span>Nueva actividad</span>
                                <AddIcon class="h-4 w-4 fill-white" />
                            </NavLink>
                        </div>
                    </template>

                    <div v-if="hasError" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-600">
                        {{ errorMessage }}
                    </div>
                    <div v-else-if="isLoading" class="space-y-4">
                        <div v-for="i in 4" :key="i" class="flex gap-4 rounded-2xl border border-slate-200/60 bg-white/60 p-6 animate-pulse">
                            <div class="h-10 w-10 rounded-full bg-slate-200"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 w-48 rounded-full bg-slate-200"></div>
                                <div class="h-3 w-3/4 rounded-full bg-slate-100"></div>
                                <div class="h-3 w-2/3 rounded-full bg-slate-100"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="filteredActivities.length" class="space-y-6">
                            <div v-for="activity in filteredActivities" :key="activity.id" class="relative flex gap-6">
                                <div class="flex flex-col items-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 border-violet-200 bg-white text-sm font-semibold text-violet-600 shadow-lg shadow-violet-200/40">
                                        {{ activity.type?.[0] ?? 'A' }}
                                    </div>
                                    <div class="mt-2 h-full w-px bg-gradient-to-b from-violet-200 to-transparent"></div>
                                </div>
                                <div class="flex-1 rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-xs uppercase tracking-widest text-slate-400">{{ activity.type ?? 'Sin tipo' }}</p>
                                            <h3 class="mt-1 text-lg font-semibold text-slate-800">{{ activity.title ?? 'Actividad sin título' }}</h3>
                                            <p class="mt-2 text-sm text-slate-500">{{ activity.description ?? 'Sin descripción registrada.' }}</p>
                                            <div class="mt-4 flex flex-wrap gap-3 text-xs text-slate-500">
                                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ formatDateTime(activity.date) }}
                                                </span>
                                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25h3.75m-3.75 3H18M15 17.25h1.5M4.5 6h15M4.5 9.75h15M4.5 13.5h6.75" />
                                                    </svg>
                                                    Estado: {{ readableState(activity) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-start gap-3 sm:items-end">
                                            <NavLink :href="route('activities.show', activity.id)" class="rounded-full border border-violet-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-violet-600 transition hover:bg-violet-600 hover:text-white">Ver</NavLink>
                                            <NavLink :href="route('activities.edit', activity.id)" class="rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">Editar</NavLink>
                                            <button @click="deleteActivity(activity.id)" class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="No hay actividades registradas" description="Programa reuniones, llamadas o tareas para mantener comunicación constante.">
                            <template #action>
                                <NavLink :href="route('activities.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                    <AddIcon class="h-4 w-4 fill-white" />
                                    Crear actividad
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
    activities: {
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

const activities = computed(() => {
    if (Array.isArray(props.activities)) return props.activities;
    return props.activities?.data ?? [];
});

const filters = reactive({
    type: 'all',
    state: 'all',
    search: '',
});

const totalActivities = computed(() => activities.value.length);
const completedActivities = computed(() => activities.value.filter(activity => ['completed', 'completada', 'done'].includes((activity.status || '').toLowerCase())).length);
const pendingActivities = computed(() => activities.value.filter(activity => ['pending', 'pendiente'].includes((activity.status || '').toLowerCase())).length);
const upcomingActivities = computed(() => activities.value.filter(activity => {
    if (!activity.date) return false;
    return new Date(activity.date) > new Date();
}).length);
const uniqueTypes = computed(() => {
    const set = new Set(activities.value.map(activity => activity.type).filter(Boolean));
    return set.size || '—';
});
const mostCommonType = computed(() => {
    if (!activities.value.length) return 'Sin datos';
    const counts = activities.value.reduce((acc, activity) => {
        const type = activity.type ?? 'Otro';
        acc[type] = (acc[type] || 0) + 1;
        return acc;
    }, {});
    const [type] = Object.entries(counts).sort((a, b) => b[1] - a[1])[0] || [];
    return type ? `Tipo más habitual: ${type}` : 'Sin datos';
});
const latestActivityDate = computed(() => {
    if (!activities.value.length) return '—';
    const latest = [...activities.value].sort((a, b) => new Date(b.date || 0) - new Date(a.date || 0))[0];
    return latest?.date ? formatDate(latest.date) : '—';
});

const activityTypes = computed(() => {
    const set = new Set(activities.value.map(activity => activity.type).filter(Boolean));
    return Array.from(set);
});

const filteredActivities = computed(() => {
    return activities.value
        .filter(activity => {
            const typeMatch = filters.type === 'all' ? true : (activity.type ?? '').toLowerCase() === filters.type.toLowerCase();
            const stateMatch = (() => {
                if (filters.state === 'all') return true;
                const state = (activity.status ?? '').toLowerCase();
                if (filters.state === 'pending') return ['pending', 'pendiente'].includes(state);
                if (filters.state === 'completed') return ['completed', 'completada', 'done'].includes(state);
                if (filters.state === 'overdue') {
                    const isPast = activity.date ? new Date(activity.date) < new Date() : false;
                    return ['pending', 'pendiente'].includes(state) && isPast;
                }
                return true;
            })();
            const searchMatch = filters.search
                ? [activity.title, activity.description].some(value => value?.toLowerCase().includes(filters.search.toLowerCase()))
                : true;
            return typeMatch && stateMatch && searchMatch;
        })
        .sort((a, b) => new Date(a.date || 0) - new Date(b.date || 0));
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

const deleteActivity = id => {
    if (confirm('¿Estás seguro de eliminar esta actividad?')) {
        Inertia.delete(route('activities.destroy', id));
    }
};

const readableState = activity => {
    const state = (activity.status ?? '').toLowerCase();
    if (['completed', 'completada', 'done'].includes(state)) return 'Completada';
    if (['pending', 'pendiente'].includes(state)) {
        if (activity.date && new Date(activity.date) < new Date()) {
            return 'Vencida';
        }
        return 'Pendiente';
    }
    return activity.status ?? 'Sin estado';
};

const formatDate = date => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString();
};

const formatDateTime = date => {
    if (!date) return '—';
    return new Date(date).toLocaleString();
};
</script>
