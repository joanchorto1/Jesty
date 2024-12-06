<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h1 class="text-lg text-blue-500 font-semibold mb-4">Editar Compañía</h1>
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Nombre</label>
                            <input v-model="form.name" type="text" class="w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Correo Electrónico</label>
                            <input v-model="form.email" type="email" class="w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Teléfono</label>
                            <input v-model="form.phone" type="text" class="w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Dirección</label>
                            <textarea v-model="form.address" class="w-full p-2 border rounded-md" rows="3"></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">NIF</label>
                            <input v-model="form.nif" type="text" class="w-full p-2 border rounded-md" required />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                            Guardar Cambios
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
    company: Object, // Datos de la compañía a editar
});

const form = ref({
    name: props.company.name,
    email: props.company.email,
    phone: props.company.phone,
    nif: props.company.nif,
    address: props.company.address,
    // website: props.company.website,
    // logo: props.company.logo,
});

// const handleLogoChange = (event) => {
//     const file = event.target.files[0];
//     if (file) {
//         const reader = new FileReader();
//         reader.onload = (e) => {
//             form.value.logo = e.target.result;
//         };
//         reader.readAsDataURL(file);
//     }
// };

const submit = () => {
    Inertia.put(route('companies.update', props.company.id), form.value);
};
</script>
