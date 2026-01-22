<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Rendimiento comercial en tiempo real"
                    description="Mide el pulso de leads, oportunidades y actividades clave para acelerar ventas."
                />

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-500">Leads</p>
                        <p class="text-3xl font-semibold mt-2">{{ totalLeads }}</p>
                        <p class="text-sm text-slate-500 mt-3">Tasa de conversión {{ conversionRate }}%</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-500">Oportunidades</p>
                        <p class="text-3xl font-semibold mt-2">{{ totalOpportunities }}</p>
                        <p class="text-sm text-slate-500 mt-3">{{ wonOpportunities }} ganadas</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-500">Notas</p>
                        <p class="text-3xl font-semibold mt-2">{{ totalNotes }}</p>
                        <p class="text-sm text-slate-500 mt-3">Última nota {{ latestNoteDate }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-500">Actividades</p>
                        <p class="text-3xl font-semibold mt-2">{{ totalActivities }}</p>
                        <p class="text-sm text-slate-500 mt-3">{{ upcomingActivities.length }} próximas</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-2 rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200/70 pb-5">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">Actividad mensual</h2>
                                <p class="text-sm text-slate-500">Seguimiento de reuniones, llamadas y tareas registradas.</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <LineChart :data="monthlyActivitiesData" />
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                        <h2 class="text-xl font-semibold text-slate-800">Próximas acciones</h2>
                        <p class="text-sm text-slate-500">Organiza tu agenda comercial a corto plazo.</p>
                        <ul class="mt-6 space-y-4 max-h-80 overflow-y-auto pr-2 text-sm text-slate-600">
                            <li v-for="activity in upcomingActivities" :key="activity.id" class="flex items-start gap-3 rounded-2xl border border-slate-200/70 bg-white/70 p-4">
                                <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                                <div>
                                    <p class="font-semibold text-slate-700">{{ activity.title ?? 'Actividad sin título' }}</p>
                                    <p class="text-xs text-slate-400">{{ formatDate(activity.date) }}</p>
                                    <p v-if="activity.type" class="text-xs text-slate-500 mt-2 uppercase tracking-widest">{{ activity.type }}</p>
                                </div>
                            </li>
                            <li v-if="upcomingActivities.length === 0" class="text-sm text-slate-400">No hay actividades programadas.</li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                        <h2 class="text-xl font-semibold text-slate-800">Origen de leads</h2>
                        <p class="text-sm text-slate-500">Identifica los canales con mejor rendimiento.</p>
                        <div class="mt-6">
                            <PieChart :data="leadSourceData" />
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                        <h2 class="text-xl font-semibold text-slate-800">Conversión de oportunidades</h2>
                        <p class="text-sm text-slate-500">Analiza el estado actual del pipeline comercial.</p>
                        <div class="mt-6">
                            <BarChart :data="opportunityConversionData" />
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                        <h2 class="text-xl font-semibold text-slate-800">Embudo de progreso</h2>
                        <p class="text-sm text-slate-500">Comprende la transición de lead a oportunidad ganada.</p>
                        <div class="mt-6">
                            <PolarChart :data="salesFunnelChart" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import LineChart from '@/Components/LineChart.vue';
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';
import PolarChart from '@/Components/PolarChart.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';

const props = defineProps({
    leads: Array,
    opportunities: Array,
    notes: Array,
    activities: Array,
});

const totalLeads = computed(() => props.leads.length);
const totalOpportunities = computed(() => props.opportunities.length);
const totalNotes = computed(() => props.notes.length);
const totalActivities = computed(() => props.activities.length);

const wonOpportunities = computed(() => props.opportunities.filter(o => o.status === 'Ganada' || o.status === 'won').length);
const conversionRate = computed(() => {
    if (!props.leads.length) {
        return 0;
    }
    return Math.round((wonOpportunities.value / props.leads.length) * 100);
});

const latestNoteDate = computed(() => {
    if (!props.notes.length) return '—';
    const latest = [...props.notes].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
    return latest?.created_at ? new Date(latest.created_at).toLocaleDateString() : '—';
});

const upcomingActivities = computed(() => {
    const now = new Date();
    return props.activities
        .filter(activity => activity.date && new Date(activity.date) >= now)
        .sort((a, b) => new Date(a.date) - new Date(b.date))
        .slice(0, 6);
});

const monthlyActivitiesData = computed(() => {
    const monthlyCounts = props.activities.reduce((acc, activity) => {
        if (!activity.date) return acc;
        const month = new Date(activity.date).toLocaleString('default', { month: 'short' });
        if (!acc[month]) acc[month] = 0;
        acc[month]++;
        return acc;
    }, {});

    return {
        labels: Object.keys(monthlyCounts),
        datasets: [
            {
                label: 'Actividades',
                backgroundColor: 'rgba(16, 185, 129, 0.4)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: false,
                data: Object.values(monthlyCounts),
            },
        ],
    };
});

const leadSourceData = computed(() => {
    const sourceCounts = props.leads.reduce((acc, lead) => {
        const source = lead.source ?? 'Desconocido';
        if (!acc[source]) acc[source] = 0;
        acc[source]++;
        return acc;
    }, {});

    return {
        labels: Object.keys(sourceCounts),
        datasets: [
            {
                data: Object.values(sourceCounts),
                backgroundColor: ['#34D399', '#38BDF8', '#6366F1', '#F59E0B', '#F472B6'],
            },
        ],
    };
});

const opportunityConversionData = computed(() => ({
    labels: ['Ganadas', 'Perdidas', 'En curso'],
    datasets: [
        {
            label: 'Oportunidades',
            backgroundColor: ['#34D399', '#F87171', '#38BDF8'],
            data: [
                props.opportunities.filter(o => ['Ganada', 'won'].includes(o.status)).length,
                props.opportunities.filter(o => ['Perdida', 'lost'].includes(o.status)).length,
                props.opportunities.filter(o => !['Ganada', 'won', 'Perdida', 'lost'].includes(o.status)).length,
            ],
        },
    ],
}));

const salesFunnelChart = computed(() => {
    const stages = {
        Leads: totalLeads.value,
        "Leads cualificados": props.leads.filter(lead => lead.status === 'qualified').length,
        "Oportunidades": totalOpportunities.value,
        "Propuestas": props.opportunities.filter(o => o.stage === 'proposal').length,
        "Ganadas": wonOpportunities.value,
    };

    return {
        labels: Object.keys(stages),
        datasets: [
            {
                data: Object.values(stages),
                backgroundColor: ['#C4B5FD', '#A5B4FC', '#818CF8', '#6366F1', '#4338CA'],
            },
        ],
    };
});

function formatDate(date) {
    return date ? new Date(date).toLocaleString() : '—';
}
</script>
