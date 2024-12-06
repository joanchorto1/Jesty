<template>
    <AppLayout>
        <div class="product-report p-6">
            <h2 class="text-xl font-semibold mb-4">Product Report</h2>

            <!-- Filtros de Fecha -->
            <div class=" mb-6 flex gap-4">
                <div>
                    <label for="startDate" class="block text-sm font-medium">Start Date</label>
                    <input type="date" v-model="startDate" id="startDate" class="mt-1 block w-full border-gray-300 rounded-md" />
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium">End Date</label>
                    <input type="date" v-model="endDate" id="endDate" class="mt-1 block w-full border-gray-300 rounded-md" />
                </div>
                <div class=" justify-end items-center  flex-col flex">
                    <button @click="filterProducts" class="px-4 py-2  bg-blue-500 text-white rounded-md">
                        Filter
                    </button>
                </div>

            </div>

            <!-- Botones de Ordenación -->
            <div class="sort-buttons mb-6 flex gap-4">
                <button @click="sortProducts('quantitySold')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md">
                    Ordenar por Cantidad Vendida
                </button>
                <button @click="sortProducts('totalSales')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md">
                    Ordenar por Total
                </button>
                <button @click="sortProducts('default')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md">
                    Orden Predeterminado
                </button>
            </div>

            <!-- Tabla de Productos Vendidos -->
            <table class="min-w-full bg-white">
                <thead>
                <tr>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad Vendida</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(product, index) in sortedProducts" :key="index" class="bg-white">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.quantitySold }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.totalSales}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<script>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    components: { AppLayout },
    props: {
        tickets: Array,
        ticketItems: Array,
        products: Array,
    },
    setup(props) {
        const startDate = ref(null);
        const endDate = ref(null);
        const sortBy = ref('default');  // Criterio de ordenación
        const filteredProducts = ref([]);

        // Función para filtrar productos vendidos según las fechas seleccionadas
        const filterProducts = () => {
            if (startDate.value && endDate.value) {
                const filteredTicketItems = props.ticketItems.filter(item => {
                    const ticket = props.tickets.find(ticket => ticket.id === item.tiket_id);
                    const ticketDate = new Date(ticket.created_at);
                    return ticketDate >= new Date(startDate.value) && ticketDate <= new Date(endDate.value);
                });

                const productSalesMap = new Map();

                filteredTicketItems.forEach(item => {
                    const product = props.products.find(product => product.id === item.product_id);
                    if (product) {
                        if (!productSalesMap.has(product.id)) {
                            productSalesMap.set(product.id, {
                                name: product.name,
                                quantitySold: 0,
                                totalSales: 0,
                            });
                        }
                        productSalesMap.get(product.id).quantitySold += item.quantity;
                        productSalesMap.get(product.id).totalSales += item.quantity * item.unit_price;
                    }
                });

                filteredProducts.value = Array.from(productSalesMap.values());
            }
        };

        // Computed para ordenar los productos según el criterio seleccionado
        const sortedProducts = computed(() => {
            if (sortBy.value === 'quantitySold') {
                return [...filteredProducts.value].sort((a, b) => b.quantitySold - a.quantitySold);
            } else if (sortBy.value === 'totalSales') {
                return [...filteredProducts.value].sort((a, b) => b.totalSales - a.totalSales);
            }
            return filteredProducts.value;
        });

        // Función para actualizar el criterio de ordenación
        const sortProducts = (criteria) => {
            sortBy.value = criteria;
        };

        return {
            startDate,
            endDate,
            filterProducts,
            filteredProducts,
            sortedProducts,
            sortProducts,
        };
    },
    filters: {
        currency(value) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
        },
    },
};
</script>
