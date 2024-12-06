<template>
    <AppLayout>
        <div class="w-full bg-gray-100 p-10">
            <!-- Sección de Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalLeads }}</p>
                    <p class="text-blue-700 font-semibold">Total de Leads</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalOpportunities }}</p>
                    <p class="text-blue-700 font-semibold">Oportunidades</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalNotes }}</p>
                    <p class="text-blue-700 font-semibold">Notas</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalActivities }}</p>
                    <p class="text-blue-700 font-semibold">Actividades</p>
                </div>
            </div>

            <!-- Gráficos de Actividades -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Actividades</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Actividades Mensuales</h3>
                    <LineChart :data="monthlyActivitiesData" />
                </div>
            </div>

            <!-- Gráficos de Leads y Oportunidades -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Leads y Oportunidades</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Leads por Origen</h3>
                    <PieChart :data="leadSourceData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Conversión de Oportunidades</h3>
                    <BarChart :data="opportunityConversionData" />
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

const props = defineProps({
    leads: Array,
    opportunities: Array,
    notes: Array,
    activities: Array,
});

// Cálculos de widgets
const totalLeads = computed(() => props.leads.length);
const totalOpportunities = computed(() => props.opportunities.length);
const totalNotes = computed(() => props.notes.length);
const totalActivities = computed(() => props.activities.length);

// Datos de gráficos
const monthlyActivitiesData = computed(() => {
    const monthlyCounts = props.activities.reduce((acc, activity) => {
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
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                data: Object.values(monthlyCounts),
            },
        ],
    };
});

const leadSourceData = computed(() => {
    const sourceCounts = props.leads.reduce((acc, lead) => {
        if (!acc[lead.source]) acc[lead.source] = 0;
        acc[lead.source]++;
        return acc;
    }, {});

    return {
        labels: Object.keys(sourceCounts),
        datasets: [
            {
                data: Object.values(sourceCounts),
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD', '#D1FAE5', '#FDE68A'],
            },
        ],
    };
});

const opportunityConversionData = computed(() => ({
    labels: ['Ganadas', 'Perdidas', 'En Curso'],
    datasets: [
        {
            label: 'Oportunidades',
            backgroundColor: ['#4CAF50', '#F44336', '#FF9800'],
            data: [
                props.opportunities.filter(o => o.status === 'Ganada').length,
                props.opportunities.filter(o => o.status === 'Perdida').length,
                props.opportunities.filter(o => o.status === 'En proceso').length,
            ],
        },
    ],
}));
</script>

<style scoped>
/* Estilo adicional */
</style>
