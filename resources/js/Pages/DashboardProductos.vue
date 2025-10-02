<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-amber-600 via-rose-600 to-purple-700 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="space-y-2">
                        <p class="text-amber-200 text-sm uppercase tracking-widest">Inventario estratégico</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Visión global del catálogo de productos</h1>
                        <p class="text-sm text-amber-200">Controla categorías, stock y rendimiento para anticiparte a la demanda.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-amber-200">Categorías</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalCategories }}</p>
                            <p class="text-sm text-amber-200 mt-3">{{ averageProductsPerCategory }} productos promedio</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-amber-200">Productos</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalProducts }}</p>
                            <p class="text-sm text-amber-200 mt-3">{{ activeProducts }} activos</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-amber-200">Stock disponible</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalStock }}</p>
                            <p class="text-sm text-amber-200 mt-3">{{ lowStockProducts.length }} productos en alerta</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-amber-200">Precio medio</p>
                            <p class="text-3xl font-semibold mt-2">€{{ averagePrice }}</p>
                            <p class="text-sm text-amber-200 mt-3">Top ventas {{ topSellingProduct }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <h2 class="text-xl font-semibold text-slate-800">Cantidad de productos por categoría</h2>
                        <p class="text-sm text-slate-500">Visualiza cómo se distribuye tu catálogo.</p>
                        <div class="mt-6">
                            <BarChart :data="productsByCategoryData" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Distribución del catálogo</h2>
                        <p class="text-sm text-slate-500">Proporción de productos por línea de negocio.</p>
                        <div class="mt-6">
                            <PieChart :data="productsCategoryDistributionData" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="bg-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-semibold text-slate-800">Rendimiento por categoría</h2>
                        <p class="text-sm text-slate-500">Ventas estimadas según la demanda registrada.</p>
                        <div class="mt-6">
                            <RadarChart :data="categorySalesRadar" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-6 xl:col-span-2">
                        <h2 class="text-xl font-semibold text-slate-800">Alertas de stock crítico</h2>
                        <p class="text-sm text-slate-500">Prioriza reposiciones con mayor impacto comercial.</p>
                        <ul class="mt-6 space-y-3 max-h-80 overflow-y-auto pr-2 text-sm text-slate-600">
                            <li v-for="product in lowStockProducts" :key="product.id" class="flex items-center justify-between rounded-2xl border border-slate-200/70 p-4">
                                <div>
                                    <p class="font-semibold text-slate-700">{{ product.name }}</p>
                                    <p class="text-xs text-slate-400">Stock actual: {{ product.stock ?? product.quantity ?? 0 }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-600">{{ product.category?.name ?? 'Sin categoría' }}</span>
                            </li>
                            <li v-if="lowStockProducts.length === 0" class="text-sm text-slate-400">No hay productos con stock crítico.</li>
                        </ul>
                    </div>
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
import RadarChart from '@/Components/RadarChart.vue';

const props = defineProps({
    categories: Array,
    products: Array,
});

const totalCategories = computed(() => props.categories.length);
const totalProducts = computed(() => props.products.length);
const averageProductsPerCategory = computed(() => props.categories.length ? Math.round(totalProducts.value / props.categories.length) : 0);
const activeProducts = computed(() => props.products.filter(product => product.status === 'active' || product.active).length);
const totalStock = computed(() => props.products.reduce((acc, product) => acc + (product.stock ?? product.quantity ?? 0), 0));
const averagePrice = computed(() => props.products.length ? (props.products.reduce((acc, product) => acc + (product.price ?? 0), 0) / props.products.length).toFixed(2) : '0.00');

const lowStockProducts = computed(() => props.products.filter(product => (product.stock ?? product.quantity ?? 0) <= 10));

const topSellingProduct = computed(() => {
    const sorted = [...props.products]
        .filter(product => product.sales !== undefined)
        .sort((a, b) => (b.sales ?? 0) - (a.sales ?? 0))[0];
    return sorted ? sorted.name : '—';
});

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
                label: 'Productos por categoría',
                backgroundColor: 'rgba(251, 191, 36, 0.5)',
                borderColor: 'rgba(251, 191, 36, 1)',
                borderWidth: 1,
                data: categoryTotals.map(ct => ct.total),
            },
        ],
    };
});

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
                label: 'Distribución de productos',
                backgroundColor: ['#FBBF24', '#F97316', '#FB7185', '#A855F7', '#6366F1'],
                data: categoryTotals.map(ct => ct.total),
            },
        ],
    };
});

const categorySalesRadar = computed(() => {
    const dataset = props.categories.map(category => {
        const productsInCategory = props.products.filter(product => product.category_id === category.id);
        const totalSales = productsInCategory.reduce((acc, product) => acc + (product.sales ?? product.stock ?? 0), 0);
        return {
            category: category.name,
            sales: totalSales,
        };
    });

    return {
        labels: dataset.map(item => item.category),
        datasets: [
            {
                label: 'Ventas estimadas',
                data: dataset.map(item => item.sales),
                backgroundColor: 'rgba(251, 146, 60, 0.35)',
                borderColor: 'rgba(249, 115, 22, 1)',
                borderWidth: 2,
            },
        ],
    };
});
</script>
