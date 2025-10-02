<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950 pb-20">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-violet-200 text-sm uppercase tracking-widest">Notas compartidas</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Captura conocimiento clave del equipo</h1>
                        <p class="text-sm text-violet-200">Documenta conversaciones, acuerdos y próximos pasos para mantener alineado al equipo comercial.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <StatsCard label="Notas" :value="totalNotes" :hint="`Últimas 7 días ${recentNotes}`" />
                        <StatsCard label="Vinculadas a leads" :value="notesByType('App\\\\Models\\\\Lead')" :hint="'Contexto comercial activo'" />
                        <StatsCard label="Vinculadas a oportunidades" :value="notesByType('App\\\\Models\\\\Opportunity')" :hint="'Seguimiento de pipeline'" />
                        <StatsCard label="Autores" :value="uniqueAuthors" :hint="topAuthorLabel" />
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 space-y-10">
                <SectionCard title="Flujo comercial" description="Las notas fortalecen la colaboración entre quienes atraen leads, gestionan oportunidades y ejecutan tareas.">
                    <FlowStepper :steps="flowSteps" :active-index="1" />
                </SectionCard>

                <SectionCard title="Notas recientes" description="Filtra por tipo de entidad o autor para acceder rápidamente a la información relevante.">
                    <template #actions>
                        <div class="flex flex-wrap items-center gap-3">
                            <select v-model="filters.relatedType" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                                <option value="all">Todas las relaciones</option>
                                <option v-for="type in relatedTypes" :key="type" :value="type">{{ readableType(type) }}</option>
                            </select>
                            <input v-model="filters.search" type="text" placeholder="Buscar por contenido o autor" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200" />
                            <NavLink :href="route('notes.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                <span>Nueva nota</span>
                                <AddIcon class="h-4 w-4 fill-white" />
                            </NavLink>
                        </div>
                    </template>

                    <div v-if="hasError" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-600">
                        {{ errorMessage }}
                    </div>
                    <div v-else-if="isLoading" class="grid grid-cols-1 gap-4">
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
                        <div v-if="filteredNotes.length" class="space-y-4">
                            <div v-for="note in filteredNotes" :key="note.id" class="rounded-2xl border border-slate-200/70 bg-white p-6 transition hover:border-violet-200 hover:shadow-lg hover:shadow-violet-200/30">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-violet-600">{{ readableType(note.noteable_type) }}</div>
                                            <p class="text-xs uppercase tracking-widest text-slate-400">{{ formatDateTime(note.created_at) }}</p>
                                        </div>
                                        <p class="text-sm text-slate-600">{{ note.content }}</p>
                                        <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a8.25 8.25 0 0115 0" />
                                                </svg>
                                                {{ note.author?.name ?? 'Autor desconocido' }}
                                            </span>
                                            <span v-if="note.tags?.length" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9A2.25 2.25 0 0118.75 7.5v9a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 16.5v-9z" />
                                                </svg>
                                                {{ note.tags.join(', ') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-start gap-3 sm:items-end">
                                        <NavLink :href="route('notes.show', note.id)" class="rounded-full border border-violet-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-violet-600 transition hover:bg-violet-600 hover:text-white">Ver</NavLink>
                                        <NavLink :href="route('notes.edit', note.id)" class="rounded-full border border-amber-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-600 transition hover:bg-amber-500 hover:text-white">Editar</NavLink>
                                        <button @click="deleteNote(note.id)" class="rounded-full border border-red-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-500 transition hover:bg-red-500 hover:text-white">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="Todavía no hay notas" description="Comparte aprendizajes clave para que el equipo avance coordinado.">
                            <template #action>
                                <NavLink :href="route('notes.create')" class="inline-flex items-center gap-2 rounded-full bg-violet-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-600/40 transition hover:bg-violet-500">
                                    <AddIcon class="h-4 w-4 fill-white" />
                                    Crear nota
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
    notes: {
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

const notes = computed(() => {
    if (Array.isArray(props.notes)) return props.notes;
    return props.notes?.data ?? [];
});

const filters = reactive({
    relatedType: 'all',
    search: '',
});

const totalNotes = computed(() => notes.value.length);
const recentNotes = computed(() => notes.value.filter(note => {
    if (!note.created_at) return false;
    const created = new Date(note.created_at);
    const now = new Date();
    const diff = (now - created) / (1000 * 60 * 60 * 24);
    return diff <= 7;
}).length);
const uniqueAuthors = computed(() => {
    const set = new Set(notes.value.map(note => note.author?.id).filter(Boolean));
    return set.size || '—';
});
const topAuthorLabel = computed(() => {
    if (!notes.value.length) return 'Sin autores registrados';
    const counts = notes.value.reduce((acc, note) => {
        const name = note.author?.name ?? 'Sin autor';
        acc[name] = (acc[name] || 0) + 1;
        return acc;
    }, {});
    const [author] = Object.entries(counts).sort((a, b) => b[1] - a[1])[0] || [];
    return author ? `Autor destacado: ${author}` : 'Sin autores registrados';
});

const notesByType = type => notes.value.filter(note => note.noteable_type === type).length;

const relatedTypes = computed(() => {
    const set = new Set(notes.value.map(note => note.noteable_type).filter(Boolean));
    return Array.from(set);
});

const filteredNotes = computed(() => {
    return notes.value.filter(note => {
        const typeMatch = filters.relatedType === 'all' ? true : note.noteable_type === filters.relatedType;
        const searchMatch = filters.search
            ? [note.content, note.author?.name, note.author?.email].some(value => value?.toLowerCase().includes(filters.search.toLowerCase()))
            : true;
        return typeMatch && searchMatch;
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

const deleteNote = id => {
    if (confirm('¿Estás seguro de eliminar esta nota?')) {
        Inertia.delete(route('notes.destroy', id));
    }
};

const readableType = type => {
    if (!type) return 'General';
    if (type.endsWith('Lead')) return 'Lead';
    if (type.endsWith('Opportunity')) return 'Oportunidad';
    if (type.endsWith('Task')) return 'Tarea';
    return type.split('\\').pop();
};

const formatDateTime = date => {
    if (!date) return '—';
    return new Date(date).toLocaleString();
};
</script>
