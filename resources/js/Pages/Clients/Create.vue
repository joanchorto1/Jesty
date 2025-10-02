<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-emerald-600 via-blue-700 to-slate-900 pb-24">
                <div class="max-w-5xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div>
                            <p class="text-emerald-200 text-sm uppercase tracking-widest">Nuevo cliente</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">Crear cliente</h1>
                            <p class="text-sm text-emerald-200 mt-2">Completa los datos para sumar un nuevo cliente a tu cartera.</p>
                        </div>
                        <NavLink :href="route('clients.index')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                            Volver al listado
                        </NavLink>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 -mt-16 pb-16">
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
