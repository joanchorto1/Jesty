<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Panel administrativo</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Resumen general</h1>
                        <p class="text-sm text-blue-200">Gestiona compañías, usuarios y monitoriza la actividad desde un mismo lugar.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <NavLink :href="route('companies.create')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 transition hover:bg-white/20">
                            Crear compañía
                        </NavLink>
                        <NavLink :href="route('users.adminCreate')" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-4 py-2 text-sm font-semibold text-white/80 backdrop-blur transition hover:bg-white/10">
                            Crear usuario
                        </NavLink>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                    <AdminStatCard label="Compañías" :value="companiesLength" description="Registradas en la plataforma" />
                    <AdminStatCard label="Usuarios totales" :value="userLength" description="Cuentas creadas" />
                    <AdminStatCard label="Usuarios activos" :value="activeUsersCount" description="Con actividad reciente" />
                    <AdminStatCard label="Estados registrados" :value="statusTypes" description="Variantes de estado de usuario" />
                </div>
            </template>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <AdminPanel title="Compañías registradas por mes" description="Visualiza el crecimiento de la red de compañías." :padding="'px-6 pb-6'">
                    <canvas ref="companiesChartRef" class="mt-2"></canvas>
                </AdminPanel>
                <AdminPanel title="Distribución de estados de usuario" description="Identifica la actividad general de los usuarios." :padding="'px-6 pb-6'">
                    <canvas ref="usersChartRef" class="mt-2"></canvas>
                </AdminPanel>
            </div>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import Chart from 'chart.js/auto';
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminStatCard from "@/Components/Dashboard/AdminStatCard.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";

const props = defineProps({
    companies: Array,
    users: Array,
});

const userLength = computed(() => props.users.length);
const companiesLength = computed(() => props.companies.length);
const activeUsersCount = computed(() => props.users.filter((user) => user.isActive).length);
const statusTypes = computed(() => new Set(props.users.map((user) => user.status ?? 'Sin estado')).size);

const companiesChartRef = ref(null);
const usersChartRef = ref(null);
let companiesChartInstance = null;
let usersChartInstance = null;

const monthlyCompanies = computed(() => {
    const summary = props.companies.reduce((acc, company) => {
        if (!company.registrationDate) {
            return acc;
        }
        const date = new Date(company.registrationDate);
        const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        acc[key] = (acc[key] || 0) + 1;
        return acc;
    }, {});
    const sortedKeys = Object.keys(summary).sort();
    return {
        labels: sortedKeys.map((key) => {
            const [year, month] = key.split('-');
            return new Date(Number(year), Number(month) - 1).toLocaleString('default', { month: 'short', year: 'numeric' });
        }),
        values: sortedKeys.map((key) => summary[key]),
    };
});

const userStatusDistribution = computed(() => {
    const summary = props.users.reduce((acc, user) => {
        const key = user.status ?? 'Sin estado';
        acc[key] = (acc[key] || 0) + 1;
        return acc;
    }, {});
    return {
        labels: Object.keys(summary),
        values: Object.values(summary),
    };
});

onMounted(() => {
    if (companiesChartRef.value) {
        companiesChartInstance = new Chart(companiesChartRef.value.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlyCompanies.value.labels,
                datasets: [
                    {
                        label: 'Compañías',
                        data: monthlyCompanies.value.values,
                        backgroundColor: 'rgba(59, 130, 246, 0.45)',
                        borderRadius: 12,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }

    if (usersChartRef.value) {
        usersChartInstance = new Chart(usersChartRef.value.getContext('2d'), {
            type: 'pie',
            data: {
                labels: userStatusDistribution.value.labels,
                datasets: [
                    {
                        label: 'Usuarios',
                        data: userStatusDistribution.value.values,
                        backgroundColor: ['#6366F1', '#22D3EE', '#F97316', '#A855F7', '#14B8A6'],
                    },
                ],
            },
            options: {
                responsive: true,
            },
        });
    }
});

onBeforeUnmount(() => {
    companiesChartInstance?.destroy();
    usersChartInstance?.destroy();
});
</script>
