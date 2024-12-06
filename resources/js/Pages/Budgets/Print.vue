<template>
    <div id="invoice" class="max-w-4xl min-h-screen mx-auto p-6 bg-white border border-gray-300">
        <!-- Header - Company Information -->
        <div class="flex   space-x-5">

             <div class="flex flex-col space-y-6 mt-8 items-start w-1/2 ">
                <!-- Company Logo -->
                                <!-- Company Details -->
                <div class="text-start">

                    <h1 class="text-2xl font-bold">{{ company.name }}</h1>
                    <p>{{ company.address }}</p>
                    <p>{{ company.phone }}</p>
                    <p>{{ company.nif }}</p>
                    <p>{{ company.email }}</p>
                </div>
            </div>

            <!-- Client Information -->
            <div class=" flex-col flex items-end w-1/2">
                <div class="mb-10">
                    <img src="/storage/JCTLogo.jpeg" alt="Company Logo" class="w-10 rounded-md h-auto" />
                </div>
                <h2 class="text-xl font-semibold ">Client Information</h2>
                <p> {{ client.name }}</p>
                <p> {{ client.address }}</p>
                <p> {{ client.phone }}</p>
                <p>{{ client.nif }}</p>
                <p>{{ client.email }}</p>
            </div>
        </div>


        <!-- Budget Details -->
        <div class="mt-5 ">
            <h2 class="text-xl  font-semibold border-b border-gray-300  ">Budget Details</h2>
            <div class="flex space-x-5">
                <p><strong>Date:</strong> {{ budget.date }}</p>
                <p><strong>Name:</strong> P1068</p>
            </div>
        </div>

        <!-- Budget Items Table -->
        <div class="mt-8">
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
                <tr v-for="item in budgetItems" :key="item.id" class="border-b">
                    <template v-for="product in products">
                        <td v-if="item.product_id === product.id" class="py-2 px-4">{{ product.name }}</td>
                    </template>
                    <td class="py-2 px-4">{{ item.quantity }}</td>
                    <td class="py-2 px-4">{{ item.unit_price }}€</td>
                    <td class="py-2 px-4">{{ item.total }}€</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="justify-end items-end h-max">
            <div class="border-t items-end justify-end border-gray-300 mt-5 pt-5 flex space-x-5">
                <!--                Desgloasar el total, calcular el iva y el total con el iva (iva 21%)-->
                <p><strong>Base Imponible:</strong> {{ budget.base_imponible }}€</p>
                <p><strong>IVA (21%):</strong> {{ budget.monto_iva  }}€</p>
                <p><strong>Total:</strong> {{ budget.total  }}€</p>

            </div>
            <button class="p-2 bg-blue-400 text-gray-50 rounded-md flex " @click="printBudget()">Imprimir <PrintIcon class="w-5 h-5 fill-blue-50 ml-5"/>      </button>
        </div>

    </div>
</template>

<script setup>
import PrintIcon from "@/Components/Icons/PrintIcon.vue";

const props = defineProps({
        company: Object,
        client: Object,
        budget: Object,
        budgetItems: Array,
         products: Array,
    });

const printBudget = () => {
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
    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    /* Other print-specific styles */
    body {
        font-size: 12px;
        line-height: 1.5;
    }

    button {
        display: none;
    }

    .invoice-container {
        padding: 10px; /* Reduce padding */
    }

    table {
        font-size: 12px; /* Reduce font size if necessary */
    }
}



h1, h2 {
    margin: 0 0 5px 0;
    font-size: 14px;
}

p {
    margin: 0 0 5px 0;
    font-size: 10px;
}

.company-logo {
    width: 60px;
    height: auto;
}

th:nth-child(2), td:nth-child(2), /* Quantity */
th:nth-child(3), td:nth-child(3), /* Unit Price */
th:nth-child(4), td:nth-child(4)  /* Total */
{
    width: 5%;
    text-align: center;
}

table, th, td {
    padding: 3px; /* Reduce padding in the table */
    border-collapse: collapse;
    border: 1px solid #000;
}

th {
    font-size: 12px; /* Smaller font for table headers */
}

td {
    font-size: 11px; /* Slightly smaller font for table data */
}
</style>
