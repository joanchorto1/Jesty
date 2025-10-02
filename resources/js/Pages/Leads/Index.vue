<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 pb-20">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-violet-200 text-sm uppercase tracking-widest">Gestión de leads</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Impulsa tus oportunidades comerciales</h1>
                        <p class="text-sm text-violet-200">Centraliza el seguimiento de leads y prepara la conversión hacia oportunidades.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <StatsCard label="Leads" :value="totalLeads" :hint="`Activos ${activeLeads}`" />
                        <StatsCard label="Conversión estimada" :value="conversionRate + '%'" :hint="`Seguidos ${followUpLeads}`" />
                        <StatsCard label="Nuevos este mes" :value="recentLeads" :hint="`Último ingreso ${latestLeadDate}`" />
                        <StatsCard label="Fuentes" :value="uniqueSources" :hint="topSourceLabel" />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 space-y-10">
                <SectionCard title="Flujo comercial" description="Controla la transición de lead a oportunidad y asegúrate de que cada contacto tenga un siguiente paso definido.">
                    <FlowStepper :steps="flowSteps" :active-index="0" />
                </SectionCard>

                <SectionCard title="Leads" description="Filtra y gestiona tu cartera actual para planificar la conversión.">
                    <template #actions>
                        <div class="flex flex-wrap items-center gap-3">
                            <input v-model="filters.search" type="text" placeholder="Buscar por nombre o empresa" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200" />
                            <select v-model="filters.status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todos los estados</option>
                                <option value="active">Activos</option>
                                <option value="qualified">Calificados</option>
                                <option value="inactive">Inactivos</option>
                            </select>
                            <select v-model="filters.source" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todas las fuentes</option>
                                <option v-for="source in sources" :key="source" :value="source">{{ source }}</option>
                            </select>
                            <NavLink :href="route('leads.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                <span>Nuevo lead</span>
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
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="filteredLeads.length" class="space-y-4">
                            <div v-for="lead in filteredLeads" :key="lead.id" class="rounded-2xl border border-slate-200/70 p-6 transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-slate-800">{{ lead.name }}</h3>
                                            <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-violet-600">
                                                {{ lead.status ?? 'Sin estado' }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">{{ lead.company ?? lead.email ?? 'Sin información de contacto' }}</p>
                                        <div class="mt-4 flex flex-wrap gap-3 text-xs text-slate-500">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.414.336-.75.75-.75h4.5a.75.75 0 01.75.75v1.5a.75.75 0 01-.75.75H4.5v9h3a.75.75 0 010 1.5h-3A1.5 1.5 0 013 18.75v-12zm12 1.5a.75.75 0 01.75-.75h4.5c.414 0 .75.336.75.75v12a1.5 1.5 0 01-1.5 1.5h-3a.75.75 0 010-1.5h3v-9h-3a.75.75 0 01-.75-.75v-1.5z" />
                                                </svg>
                                                {{ lead.source ?? 'Fuente desconocida' }}
                                            </span>
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V19.5A1.5 1.5 0 006 21h12a1.5 1.5 0 001.5-1.5V9.75M8.25 21v-6a1.5 1.5 0 011.5-1.5h4.5a1.5 1.5 0 011.5 1.5v6" />
                                                </svg>
                                                {{ lead.city ?? 'Localización no definida' }}
                                            </span>
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ formatDate(lead.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start gap-3 sm:items-end">
                                        <div class="flex gap-2">
                                            <NavLink :href="route('leads.show', lead.id)" class="rounded-full border border-violet-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-violet-600 transition hover:bg-violet-600 hover:text-white">Ver</NavLink>
                                            <NavLink :href="route('leads.edit', lead.id)" class="rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">Editar</NavLink>
                                            <button @click="deleteLead(lead.id)" class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">Eliminar</button>
                                        </div>
                                        <p class="text-xs text-slate-400">Probabilidad estimada: {{ lead.probability ? `${lead.probability}%` : 'Pendiente' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="Aún no tienes leads" description="Registra contactos comerciales y comienza a nutrir tu pipeline.">
                            <template #action>
                                <NavLink :href="route('leads.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                    <AddIcon class="h-4 w-4 fill-white" />
                                    Crear primer lead
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
    leads: {
        type: Array,
        default: () => [],
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

const filters = reactive({
    search: '',
    status: 'all',
    source: 'all',
});

const leads = computed(() => props.leads ?? []);
const totalLeads = computed(() => leads.value.length);
const activeLeads = computed(() => leads.value.filter(lead => ['active', 'qualified'].includes((lead.status || '').toLowerCase())).length);
const followUpLeads = computed(() => leads.value.filter(lead => ['follow_up', 'seguimiento'].includes((lead.status || '').toLowerCase())).length);
const recentLeads = computed(() => leads.value.filter(lead => {
    if (!lead.created_at) return false;
    const created = new Date(lead.created_at);
    const now = new Date();
    return created.getMonth() === now.getMonth() && created.getFullYear() === now.getFullYear();
}).length);
const latestLeadDate = computed(() => {
    if (!leads.value.length) return '—';
    const latest = [...leads.value].sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))[0];
    return latest?.created_at ? formatDate(latest.created_at) : '—';
});
const uniqueSources = computed(() => {
    const set = new Set(leads.value.map(lead => lead.source).filter(Boolean));
    return set.size || '—';
});
const topSourceLabel = computed(() => {
    if (!leads.value.length) return 'Sin datos de fuente';
    const counts = leads.value.reduce((acc, lead) => {
        const source = lead.source ?? 'Desconocida';
        acc[source] = (acc[source] || 0) + 1;
        return acc;
    }, {});
    const [top] = Object.entries(counts).sort((a, b) => b[1] - a[1])[0] || [];
    return top ? `Principal: ${top}` : 'Sin datos de fuente';
});
const conversionRate = computed(() => {
    if (!totalLeads.value) return 0;
    const converted = leads.value.filter(lead => ['qualified', 'opportunity', 'ganado'].includes((lead.status || '').toLowerCase())).length;
    return Math.round((converted / totalLeads.value) * 100);
});

const sources = computed(() => {
    const set = new Set(leads.value.map(lead => lead.source).filter(Boolean));
    return Array.from(set);
});

const filteredLeads = computed(() => {
    return leads.value.filter(lead => {
        const matchesSearch = filters.search
            ? [lead.name, lead.company, lead.email].some(value => value?.toLowerCase().includes(filters.search.toLowerCase()))
            : true;
        const status = (lead.status || 'sin_estado').toLowerCase();
        const matchesStatus = filters.status === 'all' ? true : status === filters.status || (filters.status === 'active' && ['active', 'activo'].includes(status));
        const matchesSource = filters.source === 'all' ? true : (lead.source ?? '').toLowerCase() === filters.source.toLowerCase();
        return matchesSearch && matchesStatus && matchesSource;
    });
});

const flowSteps = computed(() => ([
    {
        label: 'Lead',
        description: 'Captación y registro del contacto',
    },
    {
        label: 'Oportunidad',
        description: 'Calificación y valoración económica',
    },
    {
        label: 'Tareas',
        description: 'Acciones de seguimiento asignadas',
    },
]));

const isLoading = computed(() => props.isLoading);
const hasError = computed(() => Boolean(props.error));
const errorMessage = computed(() => (typeof props.error === 'string' ? props.error : props.error?.message ?? 'No se pudo cargar la información.'));

const deleteLead = leadId => {
    if (confirm('¿Estás seguro de que deseas eliminar este lead?')) {
        Inertia.delete(route('leads.destroy', leadId));
    }
};

const formatDate = date => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString();
};
</script>
