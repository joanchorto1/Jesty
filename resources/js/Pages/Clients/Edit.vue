<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-5xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Actualiza la información"
                    description="Mantén los datos al día para garantizar una comunicación fluida."
                >
                    <template #actions>
                        <NavLink :href="route('clients.show', props.client.id)" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            Ver ficha
                        </NavLink>
                    </template>
                </CrudPageHeader>

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
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
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
