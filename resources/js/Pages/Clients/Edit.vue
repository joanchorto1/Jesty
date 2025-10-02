<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-emerald-600 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-emerald-200 text-sm uppercase tracking-widest">Editar cliente</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Actualiza la información</h1>
                            <p class="text-sm text-emerald-200 mt-2">Mantén los datos al día para garantizar una comunicación fluida.</p>
                        </div>
                        <NavLink :href="route('clients.show', props.client.id)" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                            Ver ficha
                        </NavLink>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 -mt-16 pb-16">
                <Panel title="Datos del cliente" description="Revisa y confirma que toda la información sea correcta.">
                    <ClientForm
                        :form="form"
                        :processing="form.processing"
                        submit-label="Guardar cambios"
                        :companies="props.companies"
                        @submit="submit"
                        @cancel="goBack"
                        :cancel-href="route('clients.show', props.client.id)"
                    />
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Panel from '@/Components/UI/Panel.vue';
import ClientForm from '@/Components/Clients/ClientForm.vue';
import NavLink from '@/Components/NavLink.vue';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
    companies: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    company_id: props.client.company_id || '',
    name: props.client.name || '',
    nif: props.client.nif || '',
    bank: props.client.bank || '',
    phone: props.client.phone || '',
    email: props.client.email || '',
    address: props.client.address || '',
});

function submit() {
    form.put(route('clients.update', props.client.id), {
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
