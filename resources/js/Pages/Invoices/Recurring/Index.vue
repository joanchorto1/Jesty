<template>
    <AppLayout title="Facturas recurrentes">
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-6xl flex-col gap-8 px-6">
                <CrudPageHeader
                    title="Plantillas recurrentes"
                    description="Administra la programación automática de facturas, pausa temporalmente las plantillas y consulta el próximo ciclo de emisión."
                    :icon="MenuInvoiceIcon"
                />

                <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-200/70 bg-white/70 p-6 shadow-sm">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Resumen de actividad</h2>
                        <p class="text-sm text-slate-500">
                            {{ templates.length }} plantilla(s) configuradas para facturación periódica.
                        </p>
                    </div>
                    <NavLink :href="route('invoices.create')" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                        <AddIcon class="h-5 w-5" />
                        Crear desde factura
                    </NavLink>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-widest text-slate-500">
                            <tr>
                                <th class="px-6 py-3">Cliente</th>
                                <th class="px-6 py-3">Frecuencia</th>
                                <th class="px-6 py-3">Próxima ejecución</th>
                                <th class="px-6 py-3">Estado</th>
                                <th class="px-6 py-3">Última factura</th>
                                <th class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            <tr v-for="template in templates" :key="template.id" class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">{{ template.client?.name ?? 'Cliente no disponible' }}</div>
                                    <div class="text-xs text-slate-500">Plantilla #{{ template.id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    Cada {{ template.frequency_interval }} {{ frequencyLabel(template.frequency_unit) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ formatDate(template.next_run_at) ?? 'Pendiente' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="statusBadgeClasses(template.status)">{{ statusCopy(template.status) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ template.generated_invoices?.length ? formatDate(template.generated_invoices[0].date) : 'Sin emisiones' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <PrimaryButton
                                            v-if="template.status === 'active'"
                                            type="button"
                                            class="bg-amber-500 hover:bg-amber-600"
                                            @click="updateStatus(template, 'paused')"
                                        >
                                            Pausar
                                        </PrimaryButton>
                                        <PrimaryButton
                                            v-else-if="template.status === 'paused'"
                                            type="button"
                                            class="bg-emerald-600 hover:bg-emerald-700"
                                            @click="updateStatus(template, 'active')"
                                        >
                                            Reanudar
                                        </PrimaryButton>
                                        <button
                                            type="button"
                                            class="rounded-full border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                                            @click="archiveTemplate(template)"
                                        >
                                            Archivar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="templates.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">
                                    Todavía no has configurado facturas recurrentes. Crea una desde una factura nueva o existente.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import MenuInvoiceIcon from '@/Components/Icons/MenuInvoiceIcon.vue';
import AddIcon from '@/Components/Icons/AddProductIcon.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    templates: {
        type: Array,
        default: () => [],
    },
    frequencyUnits: {
        type: Array,
        default: () => [],
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
    defaultInvoiceState: {
        type: String,
        default: 'pending',
    },
});

const frequencyLabel = (unit) => {
    const map = {
        day: 'días',
        week: 'semanas',
        month: 'meses',
        year: 'años',
    };
    return map[unit] ?? unit;
};

const statusCopy = (status) => {
    const map = {
        draft: 'Borrador',
        active: 'Activa',
        paused: 'Pausada',
        completed: 'Completada',
        archived: 'Archivada',
    };
    return map[status] ?? status;
};

const statusBadgeClasses = (status) => {
    const base = 'inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold';
    switch (status) {
        case 'active':
            return `${base} bg-emerald-100 text-emerald-700`;
        case 'paused':
            return `${base} bg-amber-100 text-amber-700`;
        case 'completed':
            return `${base} bg-indigo-100 text-indigo-700`;
        case 'archived':
            return `${base} bg-slate-200 text-slate-600`;
        default:
            return `${base} bg-slate-100 text-slate-500`;
    }
};

const formatDate = (value) => {
    if (!value) {
        return null;
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return null;
    }
    return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' });
};

const updateStatus = (template, status) => {
    Inertia.patch(route('recurring-invoices.status', template.id), { status }, { preserveScroll: true });
};

const archiveTemplate = (template) => {
    if (!confirm('¿Seguro que deseas archivar esta plantilla recurrente? Esta acción detendrá nuevas emisiones.')) {
        return;
    }
    Inertia.delete(route('recurring-invoices.destroy', template.id), { preserveScroll: true });
};
</script>
