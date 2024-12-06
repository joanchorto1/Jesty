<template>
    <AppLayout>
        <div class="w-full bg-gray-100 p-10">
            <!-- Sección de Widgets Globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalTickets }}</p>
                    <p class="text-blue-700 font-semibold">Total de Tickets</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalProducts }}</p>
                    <p class="text-blue-700 font-semibold">Total de Productos</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalCategories }}</p>
                    <p class="text-blue-700 font-semibold">Total de Categorías</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalTicketItems }}</p>
                    <p class="text-blue-700 font-semibold">Total de Ítems de Tickets</p>
                </div>
            </div>

            <!-- Sección de Gráficos de Tickets -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Tickets</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Tickets por Producto</h3>
                    <PieChart :data="ticketProductData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Tickets Mensuales</h3>
                    <BarChart :data="ticketMonthlyData" />
                </div>
            </div>

            <!-- Sección de Gráficos de Productos -->
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gráficos de Productos</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Distribución de Productos por Categoría</h3>
                    <PieChart :data="productCategoryData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Productos Más Vendidos</h3>
                    <BarChart :data="topSellingProductsData" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import PieChart from '@/Components/PieChart.vue';

const props = defineProps({
    tickets: Array,
    ticketItems: Array,
    products: Array,
    categories: Array,
});

// Cálculos globales
const totalTickets = computed(() => props.tickets.length);
const totalProducts = computed(() => props.products.length);
const totalCategories = computed(() => props.categories.length);
const totalTicketItems = computed(() => props.ticketItems.length);

// Datos de Tickets
const ticketMonthlyData = computed(() => {
    const monthlyTotals = props.tickets.reduce((acc, ticket) => {
        const month = new Date(ticket.date).toLocaleString('default', { month: 'short' });
        if (!acc[month]) acc[month] = 0;
        acc[month] += ticket.total;
        return acc;
    }, {});

    return {
        labels: Object.keys(monthlyTotals),
        datasets: [
            {
                label: 'Tickets',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: Object.values(monthlyTotals),
            },
        ],
    };
});


// Datos de Productos
const productCategoryData = computed(() => {
    const categoryTotals = props.products.reduce((acc, product) => {
        if (!acc[product.category_id]) acc[product.category_id] = 0;
        acc[product.category_id] += product.sales;
        return acc;
    }, {});

    return {
        labels: Object.keys(categoryTotals).map(categoryId => {
            const category = props.categories.find(c => c.id === parseInt(categoryId));
            return category ? category.name : 'Categoría Desconocida';
        }),
        datasets: [
            {
                label: 'Productos por Categoría',
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD'],
                data: Object.values(categoryTotals),
            },
        ],
    };
});

// Productos Más Vendidos
const topSellingProductsData = computed(() => {
    const productSales = props.products.map(product => ({
        name: product.name,
        sales: product.sales,
    }));

    return {
        labels: productSales.map(p => p.name),
        datasets: [
            {
                label: 'Productos Más Vendidos',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: productSales.map(p => p.sales),
            },
        ],
    };
});
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
