<template>
    <AppLayout>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Nota de Crèdit #{{ creditNote.id }}</h1>
            <InertiaLink :href="route('credit_notes.edit', creditNote.id)" class="btn btn-primary">Editar</InertiaLink>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-medium">Detalls de la Nota de Crèdit</h2>
            <div class="space-y-2">
                <div><strong>Factura associada:</strong> <InertiaLink :href="route('invoices.show', creditNote.invoice_id)">{{ creditNote.invoice_id }}</InertiaLink></div>
                <div><strong>Total sense IVA:</strong> {{ creditNote.total_without_tax }}</div>
                <div><strong>Import IVA:</strong> {{ creditNote.tax_amount }}</div>
                <div><strong>Total amb IVA:</strong> {{ creditNote.total_with_tax }}</div>
                <div><strong>Data de creació:</strong> {{ creditNote.created_at }}</div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-medium">Ítems de la Nota de Crèdit</h2>
            <div v-if="creditNoteItems.length === 0" class="text-gray-500">No hi ha ítems per aquesta nota de crèdit.</div>
            <div v-else>
                <div v-for="(item, index) in creditNoteItems" :key="index" class="mb-4">
                    <div class="border p-4 rounded-lg shadow-sm">
                        <div><strong>Producte:</strong> {{ item.product.name }}</div>
                        <div><strong>Quantitat:</strong> {{ item.quantity }}</div>
                        <div><strong>Preu unitari:</strong> {{ item.unit_price }}</div>
                        <div><strong>Total:</strong> {{ item.total_amount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { InertiaLink } from '@inertiajs/inertia-vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
// import AppLayout from '@/Layouts/AppLayout';

export default {
    props: {
        creditNote: Object,
        creditNoteItems: Array,
    },
    components: {
        AppLayout,
        InertiaLink,
    },
};
</script>

<style scoped>
/* Afegir estil addicional si és necessari */
</style>
