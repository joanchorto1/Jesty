<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-violet-200 text-sm uppercase tracking-[0.4em]">Editar oportunidad</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Ajusta los detalles comerciales</h1>
                            <p class="text-sm text-violet-200 mt-2">
                                Actualiza los datos para reflejar la negociación y priorizar el cierre.
                            </p>
                        </div>
                        <NavLink
                            :href="route('opportunities.show', props.opportunity.id)"
                            class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition"
                        >
                            Ver ficha de la oportunidad
                        </NavLink>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <SectionCard
                    title="Seguimiento"
                    description="Comprueba en qué etapa del pipeline se encuentra esta oportunidad."
                >
                    <FlowStepper :steps="opportunitySteps" :active-index="activeStep" />
                </SectionCard>

                <SectionCard
                    title="Detalles de la oportunidad"
                    description="Revisa y ajusta la información relevante antes de guardar."
                >
                    <OpportunityForm
                        :form="form"
                        :leads="props.leads"
                        :processing="form.processing"
                        submit-label="Guardar cambios"
                        :statuses="opportunityStatuses"
                        :cancel-href="route('opportunities.show', props.opportunity.id)"
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
    opportunity: {
        type: Object,
        required: true,
    },
});

const opportunitySteps = [
    {
        label: 'Lead asignado',
        description: 'Contacto responsable y seguimiento.',
    },
    {
        label: 'Detalles comerciales',
        description: 'Valor, descripción y probabilidad.',
    },
    {
        label: 'Confirmación',
        description: 'Valida cambios y comunica al equipo.',
    },
];

const opportunityStatuses = ['En proceso', 'Ganada', 'Perdida'];

const form = useForm({
    description: props.opportunity.description || '',
    value: props.opportunity.value || '',
    probability: props.opportunity.probability ?? 50,
    status: props.opportunity.status || opportunityStatuses[0],
    lead_id: props.opportunity.lead_id || props.leads[0]?.id || '',
});

const activeStep = computed(() => {
    if (form.recentlySuccessful) {
        return 2;
    }
    return form.isDirty ? 2 : 1;
});

function submit() {
    form.put(route('opportunities.update', props.opportunity.id), {
        preserveScroll: true,
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
