<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 pb-20">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-violet-200 text-sm uppercase tracking-widest">Pipeline de oportunidades</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Convierte relaciones en negocios sostenibles</h1>
                        <p class="text-sm text-violet-200">Supervisa el estado, probabilidad y acciones relacionadas a cada oportunidad.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <StatsCard label="Oportunidades" :value="totalOpportunities" :hint="`Abiertas ${openOpportunities}`" />
                        <StatsCard label="Ganadas" :value="wonOpportunities" :hint="`Tasa ${conversionRate}%`" />
                        <StatsCard label="Valor proyectado" :value="formattedTotalValue" :hint="`Abierto ${formattedOpenValue}`" />
                        <StatsCard label="Ciclo medio" :value="`${averageCycle} días`" :hint="stageHighlight" />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 space-y-10">
                <SectionCard title="Flujo comercial" description="Mantén alineados a marketing y ventas asegurando un seguimiento continuo desde el lead hacia la ejecución de tareas.">
                    <FlowStepper :steps="flowSteps" :active-index="1" />
                </SectionCard>

                <SectionCard title="Oportunidades" description="Visualiza la salud del pipeline y prioriza las oportunidades de mayor impacto.">
                    <template #actions>
                        <div class="flex flex-wrap items-center gap-3">
                            <select v-model="filters.status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todos los estados</option>
                                <option value="Abierta">Abiertas</option>
                                <option value="Ganada">Ganadas</option>
                                <option value="Perdida">Perdidas</option>
                            </select>
                            <select v-model="filters.stage" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todas las etapas</option>
                                <option v-for="stage in stages" :key="stage" :value="stage">{{ stage }}</option>
                            </select>
                            <input v-model="filters.search" type="text" placeholder="Buscar por lead o descripción" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200" />
                            <NavLink :href="route('opportunities.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                <span>Nueva oportunidad</span>
                                <AddIcon class="h-4 w-4 fill-white" />
                            </NavLink>
                        </div>
                    </template>

                    <div v-if="hasError" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-600">
                        {{ errorMessage }}
                    </div>
                    <div v-else-if="isLoading" class="grid grid-cols-1 gap-4">
                        <div v-for="i in 3" :key="i" class="animate-pulse rounded-2xl border border-slate-200/60 bg-white/60 p-6">
                            <div class="h-5 w-32 rounded-full bg-slate-200"></div>
                            <div class="mt-4 h-6 w-1/2 rounded-full bg-slate-200"></div>
                            <div class="mt-6 space-y-2">
                                <div class="h-3 w-full rounded-full bg-slate-100"></div>
                                <div class="h-3 w-5/6 rounded-full bg-slate-100"></div>
                                <div class="h-3 w-2/3 rounded-full bg-slate-100"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="filteredOpportunities.length" class="space-y-4">
                            <div v-for="opportunity in filteredOpportunities" :key="opportunity.id" class="rounded-2xl border border-slate-200/70 p-6 transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <h3 class="text-lg font-semibold text-slate-800">{{ opportunity.description ?? 'Oportunidad sin título' }}</h3>
                                            <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-violet-600">{{ opportunity.status ?? 'Sin estado' }}</span>
                                            <span v-if="opportunity.stage" class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-blue-600">{{ opportunity.stage }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25h13.5v13.5H5.25z" />
                                                </svg>
                                                Valor: {{ formatCurrency(opportunity.value) }}
                                            </span>
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Última actualización: {{ formatDate(opportunity.updated_at || opportunity.created_at) }}
                                            </span>
                                            <span v-if="opportunity.probability" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                                </svg>
                                                Probabilidad: {{ opportunity.probability }}%
                                            </span>
                                        </div>
                                        <div class="rounded-2xl bg-slate-50 p-4">
                                            <p class="text-xs uppercase tracking-widest text-slate-400">Lead asociado</p>
                                            <p class="mt-2 text-sm font-semibold text-slate-700">{{ leadName(opportunity.lead_id) }}</p>
                                            <p class="text-xs text-slate-500">{{ leadContact(opportunity.lead_id) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start gap-3 lg:items-end">
                                        <div class="flex gap-2">
                                            <NavLink :href="route('opportunities.show', opportunity.id)" class="rounded-full border border-violet-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-violet-600 transition hover:bg-violet-600 hover:text-white">Ver</NavLink>
                                            <NavLink :href="route('opportunities.edit', opportunity.id)" class="rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">Editar</NavLink>
                                            <button @click="deleteOpportunity(opportunity.id)" class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">Eliminar</button>
                                        </div>
                                        <p class="text-xs text-slate-400">Tiempo en etapa: {{ daysInStage(opportunity) }} días</p>
                                    </div>
                                </div>

                                <div class="mt-6 border-t border-slate-100 pt-6">
                                    <p class="text-xs uppercase tracking-widest text-slate-400">Próximos pasos</p>
                                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                                        <li v-if="opportunity.next_action" class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-violet-500"></span>
                                            {{ opportunity.next_action }}
                                        </li>
                                        <li v-else class="text-slate-400">Sin tareas registradas. Crea una desde el módulo de tareas.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="No se registran oportunidades" description="Promociona leads calificados y construye propuestas para generar nuevas oportunidades.">
                            <template #action>
                                <NavLink :href="route('opportunities.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                    <AddIcon class="h-4 w-4 fill-white" />
                                    Crear oportunidad
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
    opportunities: {
        type: Array,
        default: () => [],
    },
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

const opportunities = computed(() => props.opportunities ?? []);
const leads = computed(() => props.leads ?? []);

const filters = reactive({
    status: 'all',
    stage: 'all',
    search: '',
});

const leadMap = computed(() => {
    return leads.value.reduce((acc, lead) => {
        acc[lead.id] = lead;
        return acc;
    }, {});
});

const totalOpportunities = computed(() => opportunities.value.length);
const openOpportunities = computed(() => opportunities.value.filter(o => (o.status || '').toLowerCase() === 'abierta').length);
const wonOpportunities = computed(() => opportunities.value.filter(o => (o.status || '').toLowerCase() === 'ganada').length);
const totalValue = computed(() => opportunities.value.reduce((sum, opportunity) => sum + Number(opportunity.value || 0), 0));
const openValue = computed(() => opportunities.value.filter(o => (o.status || '').toLowerCase() === 'abierta').reduce((sum, opportunity) => sum + Number(opportunity.value || 0), 0));
const conversionRate = computed(() => {
    if (!totalOpportunities.value) return 0;
    return Math.round((wonOpportunities.value / totalOpportunities.value) * 100);
});

const averageCycle = computed(() => {
    if (!opportunities.value.length) return 0;
    const days = opportunities.value.map(opportunity => {
        const created = new Date(opportunity.created_at || Date.now());
        const closed = opportunity.closed_at ? new Date(opportunity.closed_at) : new Date();
        return Math.max(1, Math.round((closed - created) / (1000 * 60 * 60 * 24)));
    });
    return Math.round(days.reduce((sum, day) => sum + day, 0) / days.length);
});

const stageHighlight = computed(() => {
    if (!opportunities.value.length) return 'Sin etapas destacadas';
    const counts = opportunities.value.reduce((acc, opportunity) => {
        const stage = opportunity.stage ?? 'Sin etapa';
        acc[stage] = (acc[stage] || 0) + 1;
        return acc;
    }, {});
    const [stage] = Object.entries(counts).sort((a, b) => b[1] - a[1])[0] || [];
    return stage ? `Etapa más frecuente: ${stage}` : 'Sin etapas destacadas';
});

const stages = computed(() => {
    const set = new Set(opportunities.value.map(opportunity => opportunity.stage).filter(Boolean));
    return Array.from(set);
});

const filteredOpportunities = computed(() => {
    return opportunities.value.filter(opportunity => {
        const status = filters.status === 'all' ? true : (opportunity.status ?? '').toLowerCase() === filters.status.toLowerCase();
        const stage = filters.stage === 'all' ? true : (opportunity.stage ?? '').toLowerCase() === filters.stage.toLowerCase();
        const text = [opportunity.description, leadName(opportunity.lead_id)].join(' ').toLowerCase();
        const search = filters.search.toLowerCase();
        return status && stage && (!search || text.includes(search));
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

const formattedTotalValue = computed(() => formatCurrency(totalValue.value));
const formattedOpenValue = computed(() => formatCurrency(openValue.value));

const deleteOpportunity = opportunityId => {
    if (confirm('¿Estás seguro de que deseas eliminar esta oportunidad?')) {
        Inertia.delete(route('opportunities.destroy', opportunityId));
    }
};

const leadName = leadId => leadMap.value[leadId]?.name ?? 'Lead no asignado';
const leadContact = leadId => leadMap.value[leadId]?.email ?? leadMap.value[leadId]?.phone ?? 'Sin contacto registrado';

const daysInStage = opportunity => {
    if (!opportunity.stage_entered_at) return 0;
    const entered = new Date(opportunity.stage_entered_at);
    const now = new Date();
    return Math.max(1, Math.round((now - entered) / (1000 * 60 * 60 * 24)));
};

function formatCurrency(value) {
    const number = Number(value || 0);
    return number.toLocaleString('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 });
}

const formatDate = date => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString();
};
</script>
