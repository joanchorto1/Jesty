<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-violet-700 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-violet-200 text-sm uppercase tracking-[0.4em]">Nuevo lead</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Registrar lead</h1>
                            <p class="text-sm text-violet-200 mt-2">
                                Completa los datos de contacto y origen para enriquecer tu pipeline comercial.
                            </p>
                        </div>
                        <NavLink
                            :href="route('leads.index')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition"
                        >
                            Volver al listado
                        </NavLink>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <SectionCard
                    title="Proceso de alta"
                    description="Sigue los pasos para garantizar que ningún dato clave quede sin registrar."
                >
                    <FlowStepper :steps="leadSteps" :active-index="activeStep" />
                </SectionCard>

                <SectionCard
                    title="Información del lead"
                    description="Con estos datos tu equipo sabrá cómo contactar, calificar y avanzar en la relación."
                >
                    <LeadForm
                        :form="form"
                        :processing="form.processing"
                        submit-label="Crear lead"
                        :statuses="leadStatuses"
                        :sources="leadSources"
                        :cancel-href="route('leads.index')"
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

const leadSteps = [
    {
        label: 'Información básica',
        description: 'Nombre, correo y teléfono de contacto.',
    },
    {
        label: 'Calificación',
        description: 'Define el estado y el origen del lead.',
    },
    {
        label: 'Confirmación',
        description: 'Revisa y guarda la información registrada.',
    },
];

const leadStatuses = ['Nuevo', 'En Proceso', 'Convertido', 'Perdido'];
const leadSources = ['Web', 'Recomendación', 'Campaña', 'Evento', 'Otros'];

const form = useForm({
    name: '',
    company_name: '',
    email: '',
    phone: '',
    position: '',
    source: '',
    status: leadStatuses[0],
});

const activeStep = computed(() => {
    if (form.recentlySuccessful) {
        return 2;
    }
    return form.isDirty ? 1 : 0;
});

function submit() {
    form.post(route('leads.store'), {
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
