<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-4 shadow-md rounded-lg">
                    <p class="text-sm text-gray-500">Total de serveis</p>
                    <p class="text-3xl font-semibold text-blue-500">{{ totalProducts }}</p>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg">
                    <p class="text-sm text-gray-500">Preu mitjà</p>
                    <p class="text-3xl font-semibold text-blue-500">{{ averagePrice }}</p>
                </div>
                <div class="bg-white p-4 shadow-md rounded-lg">
                    <p class="text-sm text-gray-500">Periodicitat més comuna</p>
                    <p class="text-xl font-semibold text-blue-500">{{ commonPeriodicity }}</p>
                </div>
            </div>

            <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-md">
                <div>
                    <h2 class="text-xl font-semibold text-gray-700">Catàleg de serveis</h2>
                    <p class="text-sm text-gray-500">Gestiona els serveis disponibles per a la facturació recurrent.</p>
                </div>
                <NavLink :href="route('products.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center space-x-2">
                    <AddIcon class="w-5 h-5"/>
                    <span>Nou servei</span>
                </NavLink>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md space-y-4">
                <div v-if="!filteredServices.length" class="text-center text-gray-500 py-10">
                    No hi ha serveis registrats encara.
                </div>
                <div v-for="service in filteredServices" :key="service.id" class="border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-700">{{ service.name }}</h3>
                        <p class="text-sm text-gray-500" v-if="service.category">{{ service.category.name }}</p>
                        <p class="mt-2 text-gray-600">{{ service.description }}</p>
                    </div>
                    <div class="flex flex-col md:items-end space-y-2">
                        <p class="text-sm text-gray-500">Preu</p>
                        <p class="text-2xl font-semibold text-blue-500">€ {{ Number(service.price).toFixed(2) }}</p>
                        <p class="text-sm text-gray-500" v-if="service.periodicity">Periodicitat: {{ service.periodicity }}</p>
                        <div class="flex space-x-3">
                            <NavLink :href="route('products.edit', service.id)" class="text-yellow-500">
                                <EditIcon class="w-5 h-5"/>
                            </NavLink>
                            <button @click="deleteProduct(service.id)" class="text-red-500">
                                <DeleteIcon class="w-5 h-5"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import AddIcon from '@/Components/Icons/AddIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    products: Array,
    categories: Array,
});

const categoryMap = computed(() => {
    return props.categories.reduce((map, category) => {
        map[category.id] = category;
        return map;
    }, {});
});

const services = computed(() => {
    return props.products.map(product => ({
        ...product,
        category: categoryMap.value[product.category_id] ?? null,
    }));
});

const filteredServices = services;

const totalProducts = computed(() => services.value.length);

const averagePrice = computed(() => {
    if (!services.value.length) {
        return '€ 0.00';
    }

    const total = services.value.reduce((sum, service) => sum + Number(service.price || 0), 0);
    return `€ ${ (total / services.value.length).toFixed(2) }`;
});

const commonPeriodicity = computed(() => {
    if (!services.value.length) {
        return 'Sense definir';
    }

    const frequency = services.value.reduce((acc, service) => {
        if (!service.periodicity) {
            return acc;
        }

        acc[service.periodicity] = (acc[service.periodicity] || 0) + 1;
        return acc;
    }, {});

    const entries = Object.entries(frequency);
    if (!entries.length) {
        return 'Sense definir';
    }

    return entries.sort((a, b) => b[1] - a[1])[0][0];
});

const deleteProduct = (id) => {
    if (confirm("Vols eliminar aquest servei?")) {
        Inertia.delete(route('products.destroy', id));
    }
};
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
