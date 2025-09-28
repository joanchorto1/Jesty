<template>
    <AppLayout>
        <div class="w-full bg-gray-100 p-10 space-y-8">
            <div class="flex flex-col space-y-2">
                <h1 class="text-3xl font-semibold text-blue-700">Panell de serveis</h1>
                <p class="text-gray-600">Una vista ràpida de l'oferta de serveis actius i la seva distribució per categories.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalCategories }}</p>
                    <p class="text-blue-700 font-semibold">Categories actives</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ totalProducts }}</p>
                    <p class="text-blue-700 font-semibold">Serveis disponibles</p>
                </div>
                <div class="bg-white shadow-lg p-4 rounded-lg text-center">
                    <p class="text-xl font-bold text-blue-300">{{ periodicityCoverage }}</p>
                    <p class="text-blue-700 font-semibold">Periodicitat definida</p>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-gray-700">Serveis per categoria</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Quantitat de serveis per categoria</h3>
                    <BarChart :data="productsByCategoryData" />
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700">Pes percentual per categoria</h3>
                    <PieChart :data="productsCategoryDistributionData" />
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
    categories: Array,
    products: Array,
});

// Cálculos globales
const totalCategories = computed(() => props.categories.length);
const totalProducts = computed(() => props.products.length);
const periodicityCoverage = computed(() => {
    if (!props.products.length) {
        return '0%';
    }

    const withPeriodicity = props.products.filter(product => Boolean(product.periodicity)).length;
    return `${Math.round((withPeriodicity / props.products.length) * 100)}%`;
});

// Datos de Productos por Categoría
const productsByCategoryData = computed(() => {
    const categoryTotals = props.categories.map(category => {
        const productsInCategory = props.products.filter(product => product.category_id === category.id);
        return {
            category: category.name,
            total: productsInCategory.length,
        };
    });

    return {
        labels: categoryTotals.map(ct => ct.category),
        datasets: [
            {
                label: 'Serveis per categoria',
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: categoryTotals.map(ct => ct.total),
            },
        ],
    };
});

// Distribución de Productos por Categoría (Gráfico Circular)
const productsCategoryDistributionData = computed(() => {
    const categoryTotals = props.categories.map(category => {
        const productsInCategory = props.products.filter(product => product.category_id === category.id);
        return {
            category: category.name,
            total: productsInCategory.length,
        };
    });

    return {
        labels: categoryTotals.map(ct => ct.category),
        datasets: [
            {
                label: 'Distribució de serveis',
                backgroundColor: ['#3B82F6', '#60A5FA', '#93C5FD', '#A5F3FC', '#BFDBFE'],
                data: categoryTotals.map(ct => ct.total),
            },
        ],
    };
});
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>

