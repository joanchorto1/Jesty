<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-5xl flex-col gap-10 px-6">
                <CrudPageHeader
                    title="Crear cliente"
                    description="Completa los datos para sumar un nuevo cliente a tu cartera."
                >
                    <template #actions>
                        <NavLink :href="route('clients.index')" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            Volver al listado
                        </NavLink>
                    </template>
                </CrudPageHeader>

                <Panel title="Datos del cliente" description="Los campos marcados como obligatorios garantizan una facturación sin incidencias.">
                    <ClientForm
                        :form="form"
                        :processing="form.processing"
                        submit-label="Crear cliente"
                        :company-name="props.company?.name ?? 'Empresa asignada'"
                        @submit="submit"
                        @cancel="goBack"
                        :cancel-href="route('clients.index')"
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
    company: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    company_id: props.company.id || '',
    name: '',
    nif: '',
    bank: '',
    phone: '',
    email: '',
    address: '',
});

function submit() {
    form.post(route('clients.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'nif', 'bank', 'phone', 'email', 'address'),
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
