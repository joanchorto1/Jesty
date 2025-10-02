<template>
    <AppLayout>
        <div :class="['min-h-screen', palette.background]">
            <div :class="['bg-gradient-to-r', palette.gradient, layout.heroWrapper]">
                <div :class="layout.heroContainer">
                    <div class="space-y-2 text-white">
                        <p :class="typography.heroKicker">Inventari estratègic</p>
                        <h1 :class="typography.heroTitle">Visió global del catàleg de productes</h1>
                        <p :class="typography.heroSubtitle">Controla categories, estoc i rendiment per anticipar la demanda.</p>
                    </div>
                    <div :class="layout.kpiGrid">
                        <InventoryKpiCard label="Categories" :value="totalCategories">
                            <template #subtitle>
                                {{ averageProductsPerCategory }} productes per categoria
                            </template>
                        </InventoryKpiCard>
                        <InventoryKpiCard label="Productes" :value="totalProducts">
                            <template #subtitle>
                                {{ activeProducts }} actius
                            </template>
                        </InventoryKpiCard>
                        <InventoryKpiCard label="Estoc disponible" :value="totalStock">
                            <template #subtitle>
                                {{ lowStockProducts.length }} productes en alerta
                            </template>
                        </InventoryKpiCard>
                        <InventoryKpiCard label="Preu mitjà" :value="`€${averagePrice}`">
                            <template #subtitle>
                                Top vendes {{ topSellingProduct }}
                            </template>
                        </InventoryKpiCard>
                    </div>
                </div>
            </div>

            <div :class="layout.bodyWrapper">
                <div :class="layout.sectionGrid">
                    <InventoryChartCard title="Quantitat per categoria" subtitle="Visualitza com es distribueix el catàleg.">
                        <BarChart :data="productsByCategoryData" />
                    </InventoryChartCard>
                    <InventoryChartCard title="Distribució del catàleg" subtitle="Proporció de productes per línia de negoci.">
                        <PieChart :data="productsCategoryDistributionData" />
                    </InventoryChartCard>
                </div>

                <div :class="layout.sectionGrid">
                    <InventoryChartCard title="Rendiment per categoria" subtitle="Vendes estimades segons la demanda registrada.">
                        <RadarChart :data="categorySalesRadar" />
                    </InventoryChartCard>
                    <InventoryChartCard title="Alertes d'estoc crític" subtitle="Prioritza reposicions amb major impacte comercial." kicker="Prioritat">
                        <ul class="mt-6 space-y-3 max-h-80 overflow-y-auto pr-2 text-sm text-slate-600">
                            <li v-for="product in lowStockProducts" :key="product.id" class="flex items-center justify-between rounded-2xl border border-slate-200/70 p-4">
                                <div>
                                    <p class="font-semibold text-slate-700">{{ product.name }}</p>
                                    <p class="text-xs text-slate-400">Estoc actual: {{ product.stock ?? product.quantity ?? 0 }}</p>
                                </div>
                                <InventoryStatusBadge :status="product.category?.name ?? 'Sense categoria'" :show-dot="false">
                                    {{ product.category?.name ?? 'Sense categoria' }}
                                </InventoryStatusBadge>
                            </li>
                            <li v-if="lowStockProducts.length === 0" class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-400">
                                No hi ha productes amb estoc crític.
                            </li>
                        </ul>
                    </InventoryChartCard>
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
import InventoryKpiCard from '@/Components/Inventory/InventoryKpiCard.vue';
import InventoryChartCard from '@/Components/Inventory/InventoryChartCard.vue';
import InventoryStatusBadge from '@/Components/Inventory/InventoryStatusBadge.vue';
import { inventoryLayout, inventoryTypography, inventoryPalette } from '@/Components/Inventory/inventoryTheme';

const props = defineProps({
    categories: Array,
    products: Array,
});

const layout = inventoryLayout;
const typography = inventoryTypography;
const palette = inventoryPalette;

const totalCategories = computed(() => props.categories.length);
const totalProducts = computed(() => props.products.length);
const averageProductsPerCategory = computed(() => (props.categories.length ? Math.round(totalProducts.value / props.categories.length) : 0));
const activeProducts = computed(() => props.products.filter(product => product.status === 'active' || product.active).length);
const totalStock = computed(() => props.products.reduce((acc, product) => acc + (product.stock ?? product.quantity ?? 0), 0));
const averagePrice = computed(() => (props.products.length ? (props.products.reduce((acc, product) => acc + (product.price ?? 0), 0) / props.products.length).toFixed(2) : '0.00'));

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
                label: 'Productes per categoria',
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
                label: 'Distribució de productes',
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
                label: 'Vendes estimades',
                data: dataset.map(item => item.sales),
                backgroundColor: 'rgba(251, 146, 60, 0.35)',
                borderColor: 'rgba(249, 115, 22, 1)',
                borderWidth: 2,
            },
        ],
    };
});
</script>
