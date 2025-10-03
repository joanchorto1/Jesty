<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-violet-200 text-sm uppercase tracking-[0.4em]">Nueva oportunidad</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Registrar oportunidad</h1>
                            <p class="text-sm text-violet-200 mt-2">
                                Define el potencial negocio, su valor estimado y la probabilidad de cierre.
                            </p>
                        </div>
                        <NavLink
                            :href="route('opportunities.index')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition"
                        >
                            Volver al pipeline
                        </NavLink>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <SectionCard
                    title="Estado del proceso"
                    description="Desde la asignación del lead hasta la estimación final de cierre."
                >
                    <FlowStepper :steps="opportunitySteps" :active-index="activeStep" />
                </SectionCard>

                <SectionCard
                    title="Detalles de la oportunidad"
                    description="Completa la información clave para priorizar y dar seguimiento."
                >
                    <OpportunityForm
                        :form="form"
                        :leads="props.leads"
                        :processing="form.processing"
                        submit-label="Crear oportunidad"
                        :statuses="opportunityStatuses"
                        :cancel-href="route('opportunities.index')"
                        @submit="submit"
                        @cancel="goBack"
                    />
                </SectionCard>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import SectionCard from '@/Components/Crm/SectionCard.vue';
import FlowStepper from '@/Components/Crm/FlowStepper.vue';
import OpportunityForm from '@/Components/Crm/OpportunityForm.vue';

const props = defineProps({
    leads: {
        type: Array,
        default: () => [],
    },
});

const opportunitySteps = [
    {
        label: 'Lead asignado',
        description: 'Selecciona el contacto responsable.',
    },
    {
        label: 'Detalles comerciales',
        description: 'Valor estimado y descripción de la oportunidad.',
    },
    {
        label: 'Probabilidad y estado',
        description: 'Evalúa la madurez del negocio.',
    },
];

const opportunityStatuses = ['En proceso', 'Ganada', 'Perdida'];

const form = useForm({
    description: '',
    value: '',
    probability: 50,
    status: opportunityStatuses[0],
    lead_id: props.leads[0]?.id ?? '',
});

const activeStep = computed(() => {
    if (form.recentlySuccessful) {
        return 2;
    }
    return form.isDirty ? 1 : 0;
});

function submit() {
    form.post(route('opportunities.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function goBack(href) {
    if (href) {
        Inertia.visit(href);
        return;
    }
    window.history.back();
}
</script>
