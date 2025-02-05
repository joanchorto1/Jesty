<template>
    <AppLayout>
        <div class="p-6 w-full bg-gray-50 min-h-screen shadow-md">
            <h1 class="text-3xl font-bold mb-8">Editar Gasto</h1>
            <form @submit.prevent="submitForm" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="date" class="block text-gray-700">Fecha</label>
                        <input v-model="expense.date" id="date" type="date"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Nombre del Gasto</label>
                        <input v-model="expense.name" id="name" type="text" placeholder="Nombre del Gasto"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="category_id" class="block text-gray-700">Categoría</label>
                        <select v-model="expense.expense_category_id" id="category_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{
                                    category.name
                                }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Descripción</label>
                        <textarea v-model="expense.description" id="description" rows="4"
                                  class="mt-1 block w-full h-11 border-gray-300 rounded-md shadow-sm"
                                  placeholder="Descripción del gasto"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="file" class="block text-gray-700">Archivo</label>

                        <!-- Vista previa si el archivo es una imagen -->
                        <div v-if="expense.file && isImage(expense.file)" class="mb-2">
                            <img :src="getFileUrl(expense.file)" alt="Archivo adjunto" class="w-40 h-auto rounded shadow">
                        </div>

                        <!-- Enlace de descarga si no es imagen -->
                        <div v-else-if="expense.file && typeof expense.file === 'string'" class="mb-2">
                            <a :href="getFileUrl(expense.file)" target="_blank" class="text-blue-600 underline">
                                Ver archivo actual
                            </a>
                        </div>

                        <!-- Input para subir archivo -->
                        <input id="file" type="file" @change="handleFileChange" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="iva" class="block text-gray-700">IVA</label>
                        <input v-model="expense.iva" id="iva" type="number" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="block text-gray-700">Monto</label>
                        <input v-model="expense.amount" id="amount" type="number" step="0.01"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="payment_method_id" class="block text-gray-700">Método de Pago</label>
                        <select v-model="expense.payment_method_id" id="payment_method_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option v-for="paymentMethod in paymentMethods" :key="paymentMethod.id" :value="paymentMethod.id">{{
                                    paymentMethod.name
                                }}</option>
                        </select>
                    </div>

                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="submit" class="bg-blue-900 p-2 rounded-full" title="Guardar Cambios">
                        <SaveIcon class="w-6 h-6 stroke-gray-50"/>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {Inertia} from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

const props = defineProps({
    categories: Array,
    paymentMethods: Array,
    expense: Object,
});

const expense = ref({
    name: '',
    description: '',
    amount: 0,
    iva: 21,
    date: new Date().toISOString().split('T')[0],
    payment_method_id: null,
    expense_category_id: null,
    file: null,
});

// Cargar datos existentes del gasto al montar el componente
onMounted(() => {
    expense.value = { ...props.expense }; // Asignar los datos actuales al formulario
});

const getFileUrl = (filePath) => {
    return `/storage/${filePath.replace('public/', '')}`;
};

const isImage = (file) => {
    return file instanceof File && file.type.startsWith('image');
};

// Manejar el cambio de archivo
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        expense.value.file = file; // Guardar el archivo en expense.file
        console.log('Archivo seleccionado:', file);
    } else {
        console.log('No se ha seleccionado ningún archivo');
    }
};

const submitForm = async () => {
    const formData = new FormData();

    for (const [key, value] of Object.entries(expense.value)) {
        if (value !== null && value !== undefined) {
            if (key === 'file' && typeof value === 'string') {
                continue; // No enviar la ruta del archivo actual
            }
            formData.append(key, value instanceof File ? value : String(value));
        }
    }

    // Enviar datos con Inertia
    Inertia.post(route('expenses.update2', props.expense.id), formData, {
        forceFormData: true, // Esto es clave para que Inertia envíe FormData correctamente
    });
};

</script>
