<template>
    <AppLayout>
    <div class="p-8 bg-gray-50 min-h-screen">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Cambiar de Plan</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                v-for="plan in plansWithFeatures"
                :key="plan.id"
                class="bg-white shadow-md rounded-lg p-6 relative"
                :class="{ 'border-4 border-blue-500': plan.id === planActual.id }"
            >
                <h2 class="text-xl font-semibold mb-4">{{ plan.name }}</h2>
                <p class="text-gray-600 mb-2">Precio: {{ plan.price }} €/ mes</p>
                <p class="text-gray-500 text-sm mb-4">{{ plan.description }}</p>
                <h3 class="text-lg font-semibold mb-2">Características</h3>
                <ul class="mb-6">
                    <li>
                        <p class="text-gray-700 flex items-center gap-2 mb-2">Limite de Usuarios:   {{plan.user_limit}}</p>

                    </li>
                    <li
                        v-for="feature in plan.features"
                        :key="feature.id"
                        class="text-gray-700 flex items-center gap-2"
                    >
                        <p>✔</p> {{ feature.name }}
                    </li>
                </ul>
                    <button
                        v-if="plan.id !== planActual.id"
                        @click="selectPlan(plan.id)"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                    >
                        Seleccionar
                    </button>
                    <span
                        v-else
                        class="absolute top-2 right-2 bg-green-100 text-green-600 px-2 py-1 rounded text-sm"
                    >
                     Plan actual
                                </span>



            </div>
        </div>
    </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {Inertia} from "@inertiajs/inertia";
const props = defineProps({
    company: Object, // Datos de la compañía
    plan: Object, // Plan actual de la compañía
    plans: Array, // Lista de planes disponibles
    features: Array, // Todas las características posibles
    planFeatures: Array, // Relación entre características y planes
});

// Computed para obtener el plan actual de la compañía
const planActual = computed(() => props.plan);

// Computed para vincular características a cada plan
const plansWithFeatures = computed(() => {
    return props.plans.map((plan) => {
        const relatedFeatures = props.planFeatures
            .filter((pf) => pf.plan_id === plan.id)
            .map((pf) => props.features.find((feature) => feature.id === pf.feature_id));
        return { ...plan, features: relatedFeatures };
    });
});

// Método para seleccionar un nuevo plan
const selectPlan = (planId) => {
    //Confirmación de elección
    if (confirm('¿Estás seguro de querer cambiar de plan?')) {
        // Redirección a la ruta de cambio de plan
        Inertia.post(route('company.updatePlan', props.company.id), { plan_id: planId });
    }
};
</script>

<style scoped>
</style>
