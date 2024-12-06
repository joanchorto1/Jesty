<template>
    <div id="invoice" class="max-w-4xl min-h-screen mx-auto  bg-white border border-gray-300">

        <!-- Header - Company Information -->
        <div class="flex p-6 space-x-5">
            <div class="flex flex-col space-y-6 mt-8 items-start w-1/2 ">
                <!-- Company Logo -->
                <div class="text-start">
                    <h1 class="text-2xl font-bold">{{ company.name }}</h1>
                    <p>{{ company.address }}</p>
                    <p>{{ company.phone }}</p>
                    <p>{{ company.nif }}</p>
                    <p>{{ company.email }}</p>
                </div>
            </div>

            <!-- Client Information -->
            <div class="flex-col flex items-end w-1/2">
                <div class="mb-10">
                    <img src="/storage/POPLogo.jpeg" alt="Company Logo" class="w-10 rounded-md h-auto" />
                </div>
                <h2 class="text-xl font-semibold">Client Information</h2>
                <p>{{ client.name }}</p>
                <p>{{ client.address }}</p>
                <p>{{ client.phone }}</p>
                <p>{{ client.nif }}</p>
                <p>{{ client.email }}</p>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="mt-5 p-6">
            <h2 class="text-xl font-semibold border-b border-gray-300">Invoice Details</h2>
            <div class="flex mt-6 space-x-5">
                <p><strong>Date:</strong> {{ invoice.date }}</p>
                <p><strong>Invoice Number:</strong>{{invoice.name }}</p>
            </div>
        </div>

        <!-- Invoice Items Table -->
        <div class="mt-8 p-6">
            <table class="w-full text-left border border-gray-300">
                <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b border-gray-300">Product</th>
                    <th class="py-2 px-4 border-b border-gray-300">Quantity</th>
                    <th class="py-2 px-4 border-b border-gray-300">Unit Price</th>
                    <th class="py-2 px-4 border-b border-gray-300">Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in invoiceItems" :key="item.id" class="border-b">
                    <template v-for="product in products">
                        <td v-if="item.product_id === product.id" class="py-2 px-4">{{ product.name }}</td>
                    </template>
                    <td class="py-2 px-4">{{ item.quantity }}</td>
                    <td class="py-2 px-4">${{ item.unit_price }}</td>
                    <td class="py-2 px-4">${{ item.total }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="justify-end items-end p-6 h-max">
            <div class="border-t items-end justify-end border-gray-300 mt-5 pt-5 flex space-x-5">
                <p><strong>Base Imponible:</strong> ${{ invoice.base_imponible }}</p>
                <p><strong>IVA (21%):</strong> ${{ invoice.monto_iva }}</p>
                <p><strong>Total:</strong> ${{ invoice.total }}</p>
            </div>
            <button class="p-2 bg-blue-400 text-gray-50 rounded-md flex" @click="printInvoice()">
                Imprimir <PrintIcon class="w-5 h-5 fill-blue-50 ml-5" />
            </button>
        </div>
    </div>
</template>

<script setup>
import PrintIcon from "@/Components/Icons/PrintIcon.vue";

const props = defineProps({
    company: Object,
    client: Object,
    invoice: Object,
    invoiceItems: Array,
    products: Array,
});

const printInvoice = () => {
    window.print();
};
</script>

<style scoped>
@media print {
    @page {
        size: A4;
        margin: 20mm;
    }

    /* Prevent table rows from breaking */


    button {
        display: none;
    }


}


</style>
