<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Planes</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Catálogo de planes</h1>
                        <p class="text-sm text-blue-200">Consulta los planes disponibles y gestiona su configuración comercial.</p>
                    </div>
                    <NavLink :href="route('plans.create')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 transition hover:bg-white/20">
                        <AddIcon class="w-4 h-4" /> Nuevo plan
                    </NavLink>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                    <AdminStatCard label="Planes activos" :value="totalPlans" :description="totalPlans === 1 ? 'Plan publicado' : 'Planes publicados'" />
                    <AdminStatCard label="Precio medio" :value="`${averagePrice} €`" description="Promedio mensual" />
                    <AdminStatCard label="Límite máximo" :value="highestUserLimit" description="Usuarios máximos por plan" />
                    <AdminStatCard label="Ingresos potenciales" :value="`${estimatedMonthly} €`" description="Basado en precio medio" />
                </div>
            </template>

            <AdminPanel title="Listado de planes" description="Gestiona cada plan y accede a sus detalles específicos.">
                <AdminTable>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre</th>
                            <th scope="col" class="px-6 py-3">Descripción</th>
                            <th scope="col" class="px-6 py-3">Precio</th>
                            <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="plan in plans" :key="plan.id" class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ plan.name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ plan.description }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ plan.price }} €</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3 text-slate-500">
                                    <NavLink :href="route('plans.show', plan.id)" class="transition hover:text-blue-500">
                                        <InfoIcon class="w-5 h-5" />
                                    </NavLink>
                                    <NavLink :href="route('plans.edit', plan.id)" class="transition hover:text-amber-500">
                                        <EditIcon class="w-5 h-5" />
                                    </NavLink>
                                    <button @click="deletePlan(plan.id)" class="transition hover:text-rose-500">
                                        <DeleteIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="plans.length === 0">
                            <td colspan="4" class="px-6 py-6 text-center text-sm text-slate-500">Aún no hay planes configurados.</td>
                        </tr>
                    </tbody>
                </AdminTable>
            </AdminPanel>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";
import AdminStatCard from "@/Components/Dashboard/AdminStatCard.vue";
import AdminTable from "@/Components/Dashboard/AdminTable.vue";

const props = defineProps({
    plans: Array,
});

const totalPlans = computed(() => props.plans.length);
const planPrices = computed(() => props.plans.map((plan) => Number(plan.price) || 0));
const averagePrice = computed(() => {
    if (!planPrices.value.length) {
        return '0.00';
    }
    const sum = planPrices.value.reduce((acc, price) => acc + price, 0);
    return (sum / planPrices.value.length).toFixed(2);
});
const highestUserLimit = computed(() => {
    if (!props.plans.length) {
        return '—';
    }
    const limits = props.plans.map((plan) => plan.user_limit ?? 0);
    const max = Math.max(...limits);
    return max > 0 ? max : '—';
});
const estimatedMonthly = computed(() => {
    if (!planPrices.value.length) {
        return '0.00';
    }
    return (planPrices.value.reduce((acc, price) => acc + price, 0)).toFixed(2);
});

const deletePlan = (planId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este plan?")) {
        Inertia.delete(route('plans.destroy', planId));
    }
};
</script>
