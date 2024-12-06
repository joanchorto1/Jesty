<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h1 class="text-lg text-blue-500 font-semibold mb-4">Crear Rol</h1>
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Nombre</label>
                            <input v-model="form.name" type="text" class="w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Descripción</label>
                            <textarea v-model="form.description" class="w-full p-2 border rounded-md" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label class="block text-gray-700 font-medium mb-1">Permisos</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="feature in features" :key="feature.id" class="flex items-center">
                                <input v-model="form.feature_ids" :value="feature.id" type="checkbox" class="mr-2" />
                                <label class="text-gray-700">{{ feature.name }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                            Crear Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    features: Array, // Lista de características disponibles
});

const form = ref({
    name: "",
    description: "",
    feature_ids: [], // IDs de las características seleccionadas
});

const submit = () => {
    Inertia.post(route("roles.store"), form.value);
};
</script>
