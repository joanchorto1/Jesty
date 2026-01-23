<template>
    <div class="min-h-screen bg-slate-100 py-8 print:bg-white print:py-0">
        <div
            id="budget"
            class="mx-auto w-full max-w-3xl rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-xl print:max-w-[190mm] print:rounded-none print:border-0 print:px-4 print:py-4 print:shadow-none"
        >
            <header class="text-center">
                <h1 class="text-lg font-semibold text-slate-900">
                    Pressupost
                </h1>
                <div class="mt-2 flex flex-wrap justify-center gap-3 text-[11px] text-slate-500">
                    <span>
                        <span class="font-semibold text-slate-600">Data:</span>
                        {{ formatDate(budget.date) }}
                    </span>
                    <span v-if="budget.due_date">
                        <span class="font-semibold text-slate-600">Venciment:</span>
                        {{ formatDate(budget.due_date) }}
                    </span>
                    <span>
                        <span class="font-semibold text-slate-600">Núm.:</span>
                        {{ budget.id }}
                    </span>
                </div>
            </header>

            <section class="mt-6 grid gap-3 md:grid-cols-2">
                <article class="rounded-xl border border-slate-200 px-4 py-4 shadow-sm">
                    <p class="text-[9px] font-semibold uppercase tracking-[0.28em] text-slate-500">Detalls de l'empresa</p>
                    <div class="mt-2 space-y-1 text-[11px] text-slate-600">
                        <p class="text-[12px] font-semibold text-slate-800">{{ company.name }}</p>
                        <p v-if="company.address">{{ company.address }}</p>
                        <p v-if="company.phone">Tel. {{ company.phone }}</p>
                        <p v-if="company.email">{{ company.email }}</p>
                        <p v-if="company.nif">ID: {{ company.nif }}</p>
                    </div>
                </article>
                <article class="rounded-xl border border-slate-200 px-4 py-4 shadow-sm">
                    <p class="text-[9px] font-semibold uppercase tracking-[0.28em] text-slate-500">Detalls del client</p>
                    <div class="mt-2 space-y-1 text-[11px] text-slate-600">
                        <p class="text-[12px] font-semibold text-slate-800">{{ client.name }}</p>
                        <p v-if="client.address">{{ client.address }}</p>
                        <p v-if="client.phone">Tel. {{ client.phone }}</p>
                        <p v-if="client.email">{{ client.email }}</p>
                        <p v-if="client.nif">ID: {{ client.nif }}</p>
                    </div>
                </article>
            </section>

            <section class="mt-6 print:mt-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-500">Detall de línies</h2>
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[9px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                        {{ items.length }} productes
                    </span>
                </div>
                <div class="mt-2 overflow-hidden rounded-xl border border-slate-200 print:rounded-lg">
                    <table class="w-full border-collapse text-[10px] leading-4 text-slate-600 print:text-[10px]">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase tracking-[0.2em] text-slate-500">
                                <th class="px-2.5 py-2 text-left">#</th>
                                <th class="px-2.5 py-2 text-left">Producte</th>
                                <th class="px-2.5 py-2 text-center">Quantitat</th>
                                <th class="px-2.5 py-2 text-center">Preu unitari</th>
                                <th class="px-2.5 py-2 text-center">Descompte</th>
                                <th class="px-2.5 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items" :key="item.id" class="border-t border-slate-200">
                                <td class="px-2.5 py-2 text-left font-medium text-slate-500">{{ item.index }}</td>
                                <td class="px-2.5 py-2 text-left text-slate-700">
                                    <span class="block font-medium text-slate-800">{{ item.productName }}</span>
                                    <span v-if="item.description" class="mt-1 block text-[9px] text-slate-500 print:text-[9px]">
                                        {{ item.description }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-2 text-center whitespace-nowrap">{{ item.quantity }}</td>
                                <td class="px-2.5 py-2 text-center whitespace-nowrap">{{ formatCurrency(item.unit_price) }}</td>
                                <td class="px-2.5 py-2 text-center whitespace-nowrap">
                                    <span v-if="item.hasDiscount">{{ formatRate(item.discount) }}</span>
                                    <span v-else class="text-slate-400">—</span>
                                </td>
                                <td class="px-2.5 py-2 text-right font-medium text-slate-700 whitespace-nowrap">
                                    {{ formatCurrency(item.total) }}
                                </td>
                            </tr>
                            <tr v-if="items.length === 0">
                                <td colspan="6" class="px-4 py-5 text-center text-[11px] text-slate-400">
                                    Encara no hi ha línies associades a aquest pressupost.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-6 flex flex-col gap-3 md:flex-row md:items-start md:justify-between print:mt-5">
                <div class="rounded-xl border border-slate-200 px-4 py-4 shadow-sm md:w-1/2">
                    <h2 class="text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-500">Resum econòmic</h2>
                    <dl class="mt-3 space-y-2 text-[11px] text-slate-600">
                        <div class="flex items-center justify-between">
                            <dt>Base imposable</dt>
                            <dd class="font-medium text-slate-800">{{ formatCurrency(budget.base_imponible) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[9px] font-semibold uppercase tracking-[0.2em] text-slate-400">IVA desglossat</dt>
                            <ul v-if="taxBreakdown.length" class="mt-2 space-y-1 text-[10px] text-slate-600">
                                <li v-for="tier in taxBreakdown" :key="tier.rate" class="flex items-center justify-between gap-3">
                                    <span>IVA {{ formatRate(tier.rate) }} · Base {{ formatCurrency(tier.base) }}</span>
                                    <span class="font-semibold text-slate-800">{{ formatCurrency(tier.tax) }}</span>
                                </li>
                            </ul>
                            <p v-else class="mt-2 text-[10px] text-slate-400">Sense IVA aplicat en aquest pressupost.</p>
                        </div>
                        <div class="flex items-center justify-between text-sm font-semibold text-slate-900">
                            <dt>Total</dt>
                            <dd>{{ formatCurrency(budget.total) }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="flex-1 rounded-xl border border-dashed border-slate-200 px-4 py-4 text-[11px] text-slate-500 shadow-sm">
                    <p class="text-[9px] font-semibold uppercase tracking-[0.3em] text-slate-400">Observacions</p>
                    <p class="mt-2 leading-relaxed" v-if="budget.notes">{{ budget.notes }}</p>
                    <p v-else class="mt-2 text-slate-400">No hi ha observacions addicionals per a aquest pressupost.</p>
                </div>
            </section>

            <footer class="mt-6 border-t border-slate-200 pt-3 text-center text-[10px] text-slate-400">
                <p>Gràcies per considerar la nostra proposta.</p>
                <p>© {{ new Date().getFullYear() }} {{ company.name }}</p>
            </footer>

            <div class="mt-6 flex justify-end print:hidden">
                <button
                    id="print-button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-700"
                    @click="downloadBudget"
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
    budget: { type: Object, default: () => ({}) },
    budgetItems: { type: Array, default: () => [] },
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

const budget = {
    ...props.budget,
    base_imponible: numberFrom(props.budget.base_imponible),
    monto_iva: numberFrom(props.budget.monto_iva),
    total: numberFrom(props.budget.total),
};

const productMap = new Map(props.products.map((product) => [product.id, product.name]));

const items = props.budgetItems.map((item, index) => {
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
            map.set(key, { rate, base: 0, tax: 0 });
        }

        const entry = map.get(key);
        entry.base = +(entry.base + base).toFixed(2);
        entry.tax = +(entry.tax + tax).toFixed(2);
    });

    return Array.from(map.values()).sort((a, b) => a.rate - b.rate);
};

const taxBreakdown = buildTaxBreakdown(items);

const downloadBudget = () => {
    const element = document.getElementById("budget");
    const button = document.getElementById("print-button");

    if (!element) {
        return;
    }

    if (button) {
        button.style.display = "none";
    }

    const options = {
        margin: 12,
        filename: `Pressupost_${props.budget.id}_${props.client.name}.pdf`,
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
