<template>
    <div class="min-h-screen bg-slate-100 py-10 print:bg-white print:py-0">
        <div
            id="invoice"
            class="mx-auto w-full max-w-3xl rounded-3xl border border-slate-200 bg-white px-10 py-12 shadow-2xl print:max-w-[190mm] print:rounded-none print:border-0 print:px-6 print:py-8 print:shadow-none"
        >
            <header class="border-b border-slate-200 pb-6">
                
                <p class="mt-2 text-sm text-slate-500">
                    <span class="font-semibold text-slate-600">Data:</span>
                    {{ formatDate(invoice.date) }}
                </p>
                <p v-if="invoice.due_date" class="text-sm text-slate-500">
                    <span class="font-semibold text-slate-600">Venciment:</span>
                    {{ formatDate(invoice.due_date) }}
                </p>
                <p v-if="invoice.name" class="text-sm text-slate-500">
                    <span class="font-semibold text-slate-600">Núm. de factura:</span>
                    {{ invoice.name }}
                </p>
            </header>

            <section class="mt-10 grid gap-6 md:grid-cols-2">
                <article class="rounded-2xl border border-slate-200 px-6 py-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Detalls de l'empresa</p>
                    <div class="mt-3 space-y-1 text-sm text-slate-600">
                        <p class="text-base font-semibold text-slate-800">{{ company.name }}</p>
                        <p v-if="company.address">{{ company.address }}</p>
                        <p v-if="company.phone">Tel. {{ company.phone }}</p>
                        <p v-if="company.email">{{ company.email }}</p>
                        <p v-if="company.nif">ID: {{ company.nif }}</p>
                    </div>
                </article>
                <article class="rounded-2xl border border-slate-200 px-6 py-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Detalls del client</p>
                    <div class="mt-3 space-y-1 text-sm text-slate-600">
                        <p class="text-base font-semibold text-slate-800">{{ client.name }}</p>
                        <p v-if="client.address">{{ client.address }}</p>
                        <p v-if="client.phone">Tel. {{ client.phone }}</p>
                        <p v-if="client.email">{{ client.email }}</p>
                        <p v-if="client.nif">ID: {{ client.nif }}</p>
                    </div>
                </article>
            </section>

            <section class="mt-12 print:mt-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Detall de línies</h2>
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">
                        {{ items.length }} productes
                    </span>
                </div>
                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 print:rounded-xl">
                    <table class="w-full border-collapse text-xs leading-5 text-slate-600 print:text-[11px]">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] uppercase tracking-[0.2em] text-blue-600">
                                <th class="px-3 py-3 text-left">Producte</th>
                                <th class="px-3 py-3 text-center">Quantitat</th>
                                <th class="px-3 py-3 text-center">Preu unitari</th>
                                <th class="px-3 py-3 text-center">Descompte</th>
                                <th class="px-3 py-3 text-center">IVA</th>
                                <th class="px-3 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items" :key="item.id" class="border-t border-slate-200">
                                <td class="px-3 py-3 text-left text-slate-700">
                                    <span class="block font-medium text-slate-800">{{ item.productName }}</span>
                                    <span v-if="item.description" class="mt-1 block text-[11px] text-slate-500 print:text-[10px]">
                                        {{ item.description }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">{{ item.quantity }}</td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">{{ formatCurrency(item.unit_price) }}</td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <span v-if="item.hasDiscount">{{ formatRate(item.discount) }}</span>
                                    <span v-else class="text-slate-400">—</span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="block font-medium text-slate-700">{{ formatRate(item.iva) }}</span>
                                    <span class="block text-[11px] text-slate-500 print:text-[10px]">{{ formatCurrency(item.ivaAmount) }}</span>
                                </td>
                                <td class="px-3 py-3 text-right font-medium text-slate-700 whitespace-nowrap">
                                    {{ formatCurrency(item.total) }}
                                </td>
                            </tr>
                            <tr v-if="items.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-400">
                                    Encara no hi ha línies associades a aquesta factura.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-10 flex flex-col gap-4 md:flex-row md:items-start md:justify-between print:mt-8">
                <div class="rounded-2xl border border-slate-200 px-6 py-5 shadow-sm md:w-1/2">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Resum econòmic</h2>
                    <dl class="mt-4 space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <dt>Base imposable</dt>
                            <dd class="font-medium text-slate-800">{{ formatCurrency(invoice.base_imponible) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">IVA desglossat</dt>
                            <div v-if="taxBreakdown.length" class="mt-2 space-y-2">
                                <div v-for="tier in taxBreakdown" :key="tier.rate" class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
                                    <p class="flex items-center justify-between text-sm text-slate-700">
                                        <span class="font-medium text-slate-800">IVA {{ formatRate(tier.rate) }}</span>
                                        <span>{{ formatCurrency(tier.tax) }}</span>
                                    </p>
                                    <p class="text-xs text-slate-500">Base: {{ formatCurrency(tier.base) }}</p>
                                </div>
                            </div>
                            <p v-else class="mt-2 text-xs text-slate-400">Sense IVA aplicat a les línies actuals.</p>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Retenció IRPF (15%)</dt>
                            <dd class="font-medium text-rose-600">− {{ formatCurrency(retencionIrpf) }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-base font-semibold text-slate-900">
                            <dt>Total a pagar</dt>
                            <dd>{{ formatCurrency(totalFinal) }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="flex-1 rounded-2xl border border-dashed border-slate-200 px-6 py-5 text-sm text-slate-500 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Observacions</p>
                    <p class="mt-3 leading-relaxed" v-if="invoice.notes">{{ invoice.notes }}</p>
                    <p v-else class="mt-3 text-slate-400">No hi ha observacions addicionals per a aquesta factura.</p>
                </div>
            </section>

            <footer class="mt-12 border-t border-slate-200 pt-6 text-center text-xs text-slate-400">
                <p>Gràcies per confiar en nosaltres.</p>
                <p>© {{ new Date().getFullYear() }} {{ company.name }}</p>
            </footer>

            <div class="mt-8 flex justify-end print:hidden">
                <button
                    id="print-button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-700"
                    @click="downloadInvoice"
                >
                    <span>Descarregar PDF</span>
                    <PrintIcon class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import html2pdf from "html2pdf.js";
import PrintIcon from "@/Components/Icons/PrintIcon.vue";

const props = defineProps({
    company: { type: Object, default: () => ({}) },
    client: { type: Object, default: () => ({}) },
    invoice: { type: Object, default: () => ({}) },
    invoiceItems: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
});

const numberFrom = (value, fallback = 0) => {
    const parsed = typeof value === "number" ? value : parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
};

const currencyFormatter = new Intl.NumberFormat("ca-ES", {
    style: "currency",
    currency: "EUR",
    minimumFractionDigits: 2,
});

const percentageFormatter = new Intl.NumberFormat("ca-ES", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
});

const dateFormatter = new Intl.DateTimeFormat("ca-ES", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
});

const formatCurrency = (value) => currencyFormatter.format(numberFrom(value));
const formatRate = (value) => `${percentageFormatter.format(numberFrom(value))}%`;
const formatDate = (value) => {
    if (!value) {
        return "—";
    }

    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? value : dateFormatter.format(date);
};

const invoice = {
    ...props.invoice,
    base_imponible: numberFrom(props.invoice.base_imponible),
    monto_iva: numberFrom(props.invoice.monto_iva),
    total: numberFrom(props.invoice.total),
};

const productMap = new Map(props.products.map((product) => [product.id, product.name]));

const items = props.invoiceItems.map((item, index) => {
    const discount = numberFrom(item.discount);

    return {
        ...item,
        index: index + 1,
        productName: productMap.get(item.product_id) ?? "—",
        description: item.description ?? "",
        quantity: numberFrom(item.quantity),
        unit_price: numberFrom(item.unit_price),
        discount,
        hasDiscount: discount > 0,
        total: numberFrom(item.total),
        iva: numberFrom(item.iva),
        ivaAmount: +((numberFrom(item.total) * numberFrom(item.iva)) / 100).toFixed(2),
    };
});

const irpfRate = 0.15;
const retencionIrpf = +(invoice.base_imponible * irpfRate).toFixed(2);
const totalFinal = +(invoice.base_imponible + invoice.monto_iva - retencionIrpf).toFixed(2);

const buildTaxBreakdown = (lineItems) => {
    const map = new Map();

    lineItems.forEach((item) => {
        const base = numberFrom(item.total);

        if (base <= 0) {
            return;
        }

        const rate = numberFrom(item.iva);
        const tax = +((base * rate) / 100).toFixed(2);
        const key = rate.toFixed(2);

        if (!map.has(key)) {
            map.set(key, { rate: rate, base: 0, tax: 0 });
        }

        const entry = map.get(key);
        entry.base = +(entry.base + base).toFixed(2);
        entry.tax = +(entry.tax + tax).toFixed(2);
    });

    return Array.from(map.values()).sort((a, b) => a.rate - b.rate);
};

const taxBreakdown = buildTaxBreakdown(items);

const downloadInvoice = () => {
    const element = document.getElementById("invoice");
    const button = document.getElementById("print-button");

    if (!element) {
        return;
    }

    if (button) {
        button.style.display = "none";
    }

    const options = {
        margin: 12,
        filename: `Factura_${props.invoice.id}_${props.client.name}.pdf`,
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    };

    html2pdf()
        .set(options)
        .from(element)
        .save()
        .finally(() => {
            if (button) {
                button.style.display = "inline-flex";
            }
        });
};
</script>

<style scoped>
</style>

