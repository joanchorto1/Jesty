<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-violet-200 text-sm uppercase tracking-[0.4em]">Actualizar lead</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Refresca la información clave</h1>
                            <p class="text-sm text-violet-200 mt-2">
                                Mantén los datos al día para que el equipo comercial siempre actúe con contexto.
                            </p>
                        </div>
                        <NavLink
                            :href="route('leads.show', props.lead.id)"
                            class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition"
                        >
                            Ver ficha del lead
                        </NavLink>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <SectionCard
                    title="Seguimiento"
                    description="Visualiza en qué punto del proceso se encuentra este lead."
                >
                    <FlowStepper :steps="leadSteps" :active-index="activeStep" />
                </SectionCard>

                <SectionCard
                    title="Información del lead"
                    description="Edita los campos necesarios para reflejar la situación actual."
                >
                    <LeadForm
                        :form="form"
                        :processing="form.processing"
                        submit-label="Guardar cambios"
                        :statuses="leadStatuses"
                        :sources="leadSources"
                        :cancel-href="route('leads.show', props.lead.id)"
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
import LeadForm from '@/Components/Crm/LeadForm.vue';

const props = defineProps({
    lead: {
        type: Object,
        required: true,
    },
});

const leadSteps = [
    {
        label: 'Información básica',
        description: 'Datos de contacto y empresa.',
    },
    {
        label: 'Calificación',
        description: 'Estado actual y origen del lead.',
    },
    {
        label: 'Confirmación',
        description: 'Validación final antes de cerrar la ficha.',
    },
];

const leadStatuses = ['Nuevo', 'En Proceso', 'Convertido', 'Perdido'];
const leadSources = ['Web', 'Recomendación', 'Campaña', 'Evento', 'Otros'];

const form = useForm({
    name: props.lead.name || '',
    company_name: props.lead.company_name || '',
    email: props.lead.email || '',
    phone: props.lead.phone || '',
    position: props.lead.position || '',
    source: props.lead.source || '',
    status: props.lead.status || leadStatuses[0],
});

const activeStep = computed(() => {
    if (form.recentlySuccessful) {
        return 2;
    }
    return form.isDirty ? 2 : 1;
});

function submit() {
    form.put(route('leads.update', props.lead.id), {
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
