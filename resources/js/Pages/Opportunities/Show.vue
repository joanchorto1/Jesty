<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 pb-20">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="mx-auto max-w-5xl px-6 pt-10">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3">
                            <p class="text-sm uppercase tracking-widest text-violet-200">Oportunidad</p>
                            <h1 class="text-3xl font-semibold text-white sm:text-4xl">{{ opportunityTitle }}</h1>
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-widest">
                                <span class="rounded-full bg-white/10 px-3 py-1 text-violet-100">{{ opportunityStatus }}</span>
                                <span v-if="relatedLead.name" class="rounded-full bg-white/10 px-3 py-1 text-violet-100">
                                    Lead: {{ relatedLead.name }}
                                </span>
                            </div>
                            <p class="text-sm text-violet-200">
                                {{ opportunityDescription }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" @click="editOpportunity" class="inline-flex items-center gap-2 rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-100 transition hover:bg-amber-400/80 hover:text-amber-900">
                                Editar
                            </button>
                            <button type="button" @click="deleteOpportunity" class="inline-flex items-center gap-2 rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-100 transition hover:bg-red-500/80 hover:text-white">
                                Eliminar
                            </button>
                        </div>
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                        <StatsCard label="Valor estimado" :value="formattedValue" :hint="`Creada ${formatDate(opportunity.created_at)}`" />
                        <StatsCard label="Probabilidad" :value="probabilityLabel" :hint="probabilityHint" />
                        <StatsCard label="Actividades" :value="activitiesCount" :hint="activitiesHint" />
                        <StatsCard label="Notas" :value="notesCount" :hint="notesHint" />
                    </div>
                </div>
            </div>

            <div class="mx-auto -mt-16 max-w-5xl space-y-10 px-6">
                <SectionCard title="Resumen de la oportunidad" description="Información clave para entender el contexto y el estado actual del negocio.">
                    <template #actions>
                        <div class="flex gap-2">
                            <button type="button" @click="editOpportunity" class="inline-flex items-center gap-2 rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">
                                Editar
                            </button>
                            <button type="button" @click="deleteOpportunity" class="inline-flex items-center gap-2 rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">
                                Eliminar
                            </button>
                        </div>
                    </template>

                    <div class="space-y-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Descripción</p>
                            <p class="mt-2 text-sm text-slate-600">{{ opportunityDescription }}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Estado y probabilidad</p>
                                <p class="mt-3 text-sm font-semibold text-slate-700">{{ opportunityStatus }}</p>
                                <p class="text-xs text-slate-500">Probabilidad estimada: {{ probabilityLabel }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Cierre estimado</p>
                                <p class="mt-3 text-sm font-semibold text-slate-700">{{ expectedCloseDate }}</p>
                                <p class="text-xs text-slate-500">Última actualización {{ formatDate(opportunity.updated_at || opportunity.created_at) }}</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Lead asociado</p>
                            <div class="mt-3 space-y-2 text-sm text-slate-600">
                                <p class="text-base font-semibold text-slate-700">{{ relatedLead.name ?? '—' }}</p>
                                <p v-if="relatedLead.company_name" class="text-xs text-slate-500">{{ relatedLead.company_name }}</p>
                                <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                                    <span v-if="relatedLead.email" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 012.25 17.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.012 1.874l-7.5 4.875a2.25 2.25 0 01-2.476 0L3.262 8.867a2.25 2.25 0 01-1.012-1.874V6.75" />
                                        </svg>
                                        {{ relatedLead.email }}
                                    </span>
                                    <span v-if="relatedLead.phone" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.414.336-.75.75-.75h3.375c.31 0 .582.187.696.475l1.434 3.586a.75.75 0 01-.17.79l-2.21 2.21a11.048 11.048 0 006.12 6.12l2.21-2.21a.75.75 0 01.79-.17l3.586 1.434a.75.75 0 01.475.696V21a.75.75 0 01-.75.75H17.25C9.55 21.75 2.25 14.45 2.25 6.75z" />
                                        </svg>
                                        {{ relatedLead.phone }}
                                    </span>
                                    <span v-if="relatedLead.source" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ relatedLead.source }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </SectionCard>

                <SectionCard title="Actividades" description="Controla cada interacción para asegurar el seguimiento comercial.">
                    <template #actions>
                        <button type="button" @click="createActivity" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-lg shadow-violet-600/30 transition hover:bg-violet-500">
                            <AddIcon class="h-4 w-4 fill-white" />
                            Nueva actividad
                        </button>
                    </template>

                    <div v-if="activitiesList.length" class="space-y-4">
                        <div v-for="activity in activitiesList" :key="activity.id" class="rounded-2xl border border-slate-200/70 p-5 transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-violet-600">{{ activity.type ?? 'Actividad' }}</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-500">{{ formatDate(activity.date) }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600">{{ activity.notes ?? 'Sin comentarios adicionales.' }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="editActivity(activity)" class="inline-flex items-center gap-2 rounded-full border border-amber-200 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">
                                        Editar
                                    </button>
                                    <button type="button" @click="deleteActivity(activity)" class="inline-flex items-center gap-2 rounded-full border border-red-200 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <EmptyState v-else title="No hay actividades registradas" description="Registra llamadas, reuniones y seguimientos para mantener el pipeline actualizado.">
                        <template #action>
                            <button type="button" @click="createActivity" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/30 transition hover:bg-violet-500">
                                <AddIcon class="h-4 w-4 fill-white" />
                                Crear actividad
                            </button>
                        </template>
                    </EmptyState>
                </SectionCard>

                <SectionCard title="Notas" description="Comparte contexto relevante con el equipo de ventas.">
                    <template #actions>
                        <button type="button" @click="createNote" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-lg shadow-violet-600/30 transition hover:bg-violet-500">
                            <AddIcon class="h-4 w-4 fill-white" />
                            Nueva nota
                        </button>
                    </template>

                    <div v-if="notesList.length" class="space-y-4">
                        <div v-for="note in notesList" :key="note.id" class="rounded-2xl border border-slate-200/70 p-5 transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <p class="text-sm text-slate-600">{{ note.content }}</p>
                                <div class="flex gap-2">
                                    <button type="button" @click="editNote(note)" class="inline-flex items-center gap-2 rounded-full border border-amber-200 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">
                                        Editar
                                    </button>
                                    <button type="button" @click="deleteNote(note)" class="inline-flex items-center gap-2 rounded-full border border-red-200 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <EmptyState v-else title="No hay notas aún" description="Añade comentarios y aprendizajes clave para agilizar la toma de decisiones.">
                        <template #action>
                            <button type="button" @click="createNote" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/30 transition hover:bg-violet-500">
                                <AddIcon class="h-4 w-4 fill-white" />
                                Crear nota
                            </button>
                        </template>
                    </EmptyState>
                </SectionCard>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatsCard from '@/Components/Crm/StatsCard.vue';
import SectionCard from '@/Components/Crm/SectionCard.vue';
import EmptyState from '@/Components/Crm/EmptyState.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';

const props = defineProps({
    opportunity: {
        type: Object,
        required: true,
    },
    activities: {
        type: Array,
        default: () => [],
    },
    notes: {
        type: Array,
        default: () => [],
    },
    leads: {
        type: Array,
        default: () => [],
    },
});

const opportunity = computed(() => props.opportunity ?? {});
const activitiesList = computed(() => [...(props.activities ?? [])].sort((a, b) => new Date(b.date || b.created_at || 0) - new Date(a.date || a.created_at || 0)));
const notesList = computed(() => [...(props.notes ?? [])].sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0)));
const relatedLead = computed(() => props.leads?.find(lead => lead.id === opportunity.value.lead_id) ?? {});

const opportunityTitle = computed(() => opportunity.value.description ?? 'Oportunidad sin título');
const opportunityDescription = computed(() => opportunity.value.description ?? 'Aún no se ha añadido una descripción.');
const opportunityStatus = computed(() => opportunity.value.status ?? 'Sin estado');
const formattedValue = computed(() => formatCurrency(opportunity.value.value));
const probabilityLabel = computed(() => (opportunity.value.probability !== null && opportunity.value.probability !== undefined ? `${opportunity.value.probability}%` : 'Sin datos'));
const probabilityHint = computed(() => relatedLead.value?.company_name ? `Lead ${relatedLead.value.company_name}` : 'Seguimiento activo');
const activitiesCount = computed(() => activitiesList.value.length);
const notesCount = computed(() => notesList.value.length);
const activitiesHint = computed(() => (activitiesList.value.length ? `Última ${formatDate(activitiesList.value[0].date || activitiesList.value[0].created_at)}` : 'Sin actividades')); 
const notesHint = computed(() => (notesList.value.length ? `Última ${formatDate(notesList.value[0].created_at)}` : 'Sin notas'));
const expectedCloseDate = computed(() => opportunity.value.expected_close_date ? formatDate(opportunity.value.expected_close_date) : 'Sin definir');

const formatCurrency = value => {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    const number = Number(value);
    if (Number.isNaN(number)) {
        return value;
    }

    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0,
    }).format(number);
};

const formatDate = date => {
    if (!date) {
        return '—';
    }

    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const createActivity = () => {
    Inertia.visit(route('activities.goToCreate', {
        lead: opportunity.value.lead_id,
        opportunity: opportunity.value.id,
    }));
};

const createNote = () => {
    Inertia.visit(route('notes.goToCreate', {
        opportunity: opportunity.value.id,
        lead: opportunity.value.lead_id,
    }));
};

const editOpportunity = () => {
    Inertia.visit(route('opportunities.goToEdit', { id: opportunity.value.id }));
};

const deleteOpportunity = () => {
    if (confirm('¿Estás seguro de que deseas eliminar esta oportunidad?')) {
        Inertia.delete(route('opportunities.destroy', { id: opportunity.value.id }));
    }
};

const editActivity = activity => {
    Inertia.visit(route('activities.goToEdit', { id: activity.id }));
};

const deleteActivity = activity => {
    if (confirm('¿Estás seguro de que deseas eliminar esta actividad?')) {
        Inertia.delete(route('activities.destroy', { id: activity.id }));
    }
};

const editNote = note => {
    Inertia.visit(route('notes.goToEdit', { id: note.id }));
};

const deleteNote = note => {
    if (confirm('¿Estás seguro de que deseas eliminar esta nota?')) {
        Inertia.delete(route('notes.destroy', { id: note.id }));
    }
};
</script>
