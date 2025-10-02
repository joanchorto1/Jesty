<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Planes</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Cambiar plan</h1>
                        <p class="text-sm text-blue-200 max-w-2xl">Selecciona la suscripción que mejor se ajuste a las necesidades de tu compañía.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <AdminStatCard label="Plan actual" :value="plan.name" :description="`${plan.price} €/mes`">
                            <template #badge>
                                <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-200 ring-1 ring-emerald-400/30">Activo</span>
                            </template>
                        </AdminStatCard>
                    </div>
                </div>
            </template>

            <AdminPanel title="Planes disponibles" description="Compara las características y selecciona el plan que quieras activar.">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <article
                        v-for="option in plansWithFeatures"
                        :key="option.id"
                        :class="planCardClasses(option.id === currentPlanId)"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">{{ option.name }}</h2>
                                <p class="text-sm text-slate-500">{{ option.description }}</p>
                            </div>
                            <span v-if="option.id === currentPlanId" class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-600">Plan actual</span>
                        </div>
                        <p class="mt-4 text-3xl font-semibold text-slate-900">{{ option.price }}<span class="text-base font-medium text-slate-500"> €/mes</span></p>
                        <p class="mt-2 text-sm text-slate-500">Capacidad de usuarios: <span class="font-semibold text-slate-700">{{ option.user_limit }}</span></p>
                        <ul class="mt-4 space-y-2 text-sm text-slate-600">
                            <li v-for="feature in option.features" :key="feature.id" class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                <span>{{ feature.name }}</span>
                            </li>
                            <li v-if="!option.features.length" class="text-xs text-slate-400">Sin características asociadas.</li>
                        </ul>
                        <button
                            v-if="option.id !== currentPlanId"
                            @click="selectPlan(option.id)"
                            class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition"
                        >
                            Seleccionar plan
                        </button>
                    </article>
                </div>
            </AdminPanel>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";
import AdminStatCard from "@/Components/Dashboard/AdminStatCard.vue";

const props = defineProps({
    company: Object,
    plan: Object,
    plans: Array,
    features: Array,
    planFeatures: Array,
});

const currentPlanId = computed(() => props.plan?.id ?? null);

const plansWithFeatures = computed(() => {
    return props.plans.map((planOption) => {
        const relatedFeatures = props.planFeatures
            .filter((pf) => pf.plan_id === planOption.id)
            .map((pf) => props.features.find((feature) => feature.id === pf.feature_id))
            .filter(Boolean);
        return { ...planOption, features: relatedFeatures };
    });
});

const planCardClasses = (isActive) => [
    'flex flex-col rounded-3xl border px-6 py-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg',
    isActive ? 'border-blue-400 bg-blue-50/60 ring-2 ring-blue-300/60' : 'border-slate-200 bg-white',
];

const selectPlan = (planId) => {
    if (confirm('¿Estás seguro de querer cambiar de plan?')) {
        Inertia.post(route('company.updatePlan', props.company.id), { plan_id: planId });
    }
};
</script>
