<template>
    <div id="invoice" class="max-w-4xl min-h-screen mx-auto p-6 bg-white shadow-lg rounded-lg">
        <!-- Header - Company Information -->
        <div class="flex justify-between mb-10">
            <!-- Company Details -->
            <div>
                <img src="/storage/JCTLogo.jpeg" alt="Company Logo" class="w-16 rounded-md mb-4" />
                <h1 class="text-xl font-bold">{{ company.name }}</h1>
                <p class="text-gray-600">{{ company.address }}</p>
                <p class="text-gray-600">{{ company.phone }}</p>
                <p class="text-gray-600">{{ company.nif }}</p>
                <p class="text-gray-600">{{ company.email }}</p>
            </div>
            <!-- Client Information -->
            <div class="text-right mt-20">
                <h2 class="text-xl font-semibold text-gray-800">Informació del client</h2>
                <p class="text-gray-600">{{ client.name }}</p>
                <p class="text-gray-600">{{ client.address }}</p>
                <p class="text-gray-600">{{ client.phone }}</p>
                <p class="text-gray-600">{{ client.nif }}</p>
                <p class="text-gray-600">{{ client.email }}</p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-300 pb-2">Detalls de la Factura</h2>
            <div class="mt-4 text-gray-600">

                <p><strong>Data:</strong> {{ invoice.date }}</p>
                <p><strong>Nº Factura:</strong> {{ invoice.name }}</p>
<!--                <p><strong>ID:</strong> F{{ invoice.id }}</p>-->
            </div>
        </div>

        <!-- Invoice Items Table -->
        <div>
            <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                <thead>
                <tr class="bg-blue-50 text-blue-600">
                    <th class="py-2 px-4 border border-gray-300 text-left">Producte</th>
                    <th class="py-2 px-4 border border-gray-300 text-center">Quantitat</th>
                    <th class="py-2 px-4 border border-gray-300 text-center">Preu Unitari</th>
                    <th class="py-2 px-4 border border-gray-300 text-center">Descompte</th>
                    <th class="py-2 px-4 border border-gray-300 text-center">Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in invoiceItems" :key="item.id" class="border-b hover:bg-gray-50">
                    <template v-for="product in products">
                        <td v-if="item.product_id === product.id" class="py-2 px-4 text-gray-700">{{ product.name }}</td>
                    </template>
                    <td class="py-2 px-4 text-center text-gray-700">{{ item.quantity }}</td>
                    <td class="py-2 px-4 text-center text-gray-700">{{ item.unit_price }}€</td>
                    <td v-if="item.discount != 0" class="py-2 px-4 text-center text-gray-700">{{ item.discount }}%</td>
                    <td v-else class="py-2 px-4 text-center text-gray-500">-</td>
                    <td class="py-2 px-4 text-center text-gray-700">{{ item.total }}€</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Invoice Totals -->
        <div class="flex justify-end mt-8 text-gray-800">
            <div class="text-right">
                <p class="mb-2"><strong>Base Imponible:</strong> {{ invoice.base_imponible }}€</p>
                <p class="mb-2"><strong>IVA (21%):</strong> {{ invoice.monto_iva }}€</p>
                <p class="mb-2"><strong>Retenció IRPF (15%):</strong> −{{ retencionIrpf.toFixed(2) }}€</p>
                <p class="text-lg font-bold mt-2"><strong>Total a pagar:</strong> {{ totalFinal.toFixed(2) }}€</p>
            </div>
        </div>

        <!-- Print Button -->
        <div class="mt-6 flex justify-end">
            <button
                id="print-button"
                class="flex items-center space-x-2 bg-blue-500 text-white py-2 px-4 rounded-md shadow-md hover:bg-blue-600 transition"
                @click="printBudget"
            >
                <span>Imprimir</span>
                <PrintIcon class="w-5 h-5 fill-current" />
            </button>
        </div>
    </div>
</template>

<script setup>
import html2pdf from "html2pdf.js";
import PrintIcon from "@/Components/Icons/PrintIcon.vue";

const props = defineProps({
    company: Object,
    client: Object,
    invoice: Object,
    invoiceItems: Array,
    products: Array,
});

// Format price fields
const invoiceItems = props.invoiceItems.map((item) => {
    return {
        ...item,
        unit_price: item.unit_price.toFixed(2),
        total: item.total.toFixed(2),
    };
});

// Calcular IRPF
const irpfRate = 0.15;
const retencionIrpf = +(props.invoice.base_imponible * irpfRate).toFixed(2);
const totalFinal = +(
    props.invoice.base_imponible +
    props.invoice.monto_iva -
    retencionIrpf
).toFixed(2);

// Format invoice fields
const invoice = {
    ...props.invoice,
    base_imponible: props.invoice.base_imponible.toFixed(2),
    monto_iva: props.invoice.monto_iva.toFixed(2),
    total: props.invoice.total.toFixed(2),
};

const printBudget = () => {
    const element = document.getElementById("invoice");
    const printButton = document.getElementById("print-button");
    printButton.style.display = "none";

    const options = {
        margin: 10,
        filename: `Factura${props.invoice.id}_${props.client.name}.pdf`,
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    };

    html2pdf()
        .set(options)
        .from(element)
        .save()
        .finally(() => {
            printButton.style.display = "block";
        });
};
</script>

<style scoped>
</style>

