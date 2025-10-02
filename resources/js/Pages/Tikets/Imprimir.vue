<template>
    <div class="min-h-screen bg-slate-950 py-12 print:bg-white print:py-0">
        <div class="mx-auto max-w-md px-4">
            <div id="ticket" class="rounded-3xl border border-slate-200 bg-white p-8 shadow-2xl print:border-0 print:shadow-none">
                <header class="text-center">
                    <h1 class="text-2xl font-semibold text-slate-900">{{ company.name }}</h1>
                    <p class="text-sm text-slate-500">{{ company.address }}</p>
                    <p class="text-sm text-slate-500">Tel: {{ company.phone }} · NIF: {{ company.nif }}</p>
                    <p class="text-sm text-slate-500">{{ company.email }}</p>
                    <div class="mt-6 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        <p class="font-semibold text-slate-800">Ticket #{{ tiket.id }}</p>
                        <p>{{ formatDate(tiket.created_at) }}</p>
                    </div>
                </header>

                <main class="mt-6 space-y-6">
                    <section>
                        <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Detall d'articles</h2>
                        <table class="mt-3 w-full text-sm text-slate-600">
                            <thead>
                                <tr class="text-left text-xs font-semibold uppercase tracking-widest text-slate-400">
                                    <th class="pb-2">Producte</th>
                                    <th class="pb-2 text-center">Quant.</th>
                                    <th class="pb-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="item in tiketItems" :key="item.id" class="py-2">
                                    <td class="py-2 font-semibold text-slate-800">{{ resolveProductName(item.product_id) }}</td>
                                    <td class="py-2 text-center">{{ item.quantity }}</td>
                                    <td class="py-2 text-right">{{ formatCurrency(item.total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </section>

                    <section class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between"><span>Base imposable</span><span class="font-semibold text-slate-800">{{ formatCurrency(tiket.base_imponible) }}</span></div>
                        <div class="flex items-center justify-between"><span>IVA ({{ tiket.iva }}%)</span><span class="font-semibold text-slate-800">{{ formatCurrency(tiket.monto_iva) }}</span></div>
                        <div class="mt-2 flex items-center justify-between text-base font-semibold text-slate-900">
                            <span>Total</span>
                            <span>{{ formatCurrency(tiket.total) }}</span>
                        </div>
                    </section>
                </main>

                <footer class="mt-6 text-center text-xs text-slate-400">
                    <p>Gràcies per la teva compra!</p>
                </footer>
            </div>

            <div class="mt-6 flex justify-center gap-3 print:hidden">
                <button
                    class="rounded-2xl bg-gradient-to-r from-fuchsia-500 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-purple-500/20 transition hover:from-fuchsia-400 hover:to-purple-500"
                    @click="printTicket"
                >
                    Imprimir
                </button>
                <button
                    class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-fuchsia-300 hover:text-fuchsia-500"
                    @click="goBack"
                >
                    Tornar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    company: Object,
    tiket: Object,
    tiketItems: Array,
    products: Array,
});

const formatCurrency = value => new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(Number(value) || 0);
const formatDate = value => new Date(value).toLocaleString();

const resolveProductName = productId => {
    const product = props.products.find(product => product.id === productId);
    return product ? product.name : 'Producte';
};

const printTicket = () => {
    window.print();
};

const goBack = () => {
    Inertia.visit(route('tikets.index'));
};
</script>

<style scoped>
@media print {
    body {
        background: white !important;
    }
    #ticket {
        width: 80mm;
        padding: 0;
    }
}
</style>
