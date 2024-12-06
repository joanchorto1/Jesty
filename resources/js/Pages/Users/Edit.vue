<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h1 class="text-lg text-blue-500 font-semibold mb-4">Editar Usuario</h1>
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Nombre</label>
                            <input v-model="form.name" type="text" class="w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Email</label>
                            <input v-model="form.email" type="email" class="w-full p-2 border rounded-md" required />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Teléfono</label>
                            <input v-model="form.phone" type="text" class="w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Contraseña</label>
                            <input v-model="form.password" type="password" class="w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Dirección</label>
                            <input v-model="form.address" type="text" class="w-full p-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Rol</label>
                            <select v-model="form.role_id" class="w-full p-2 border rounded-md" required>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
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
    user: Object, // Usuario a editar
    roles: Array, // Lista de roles disponibles
});

const form = ref({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    password: "",
    address: props.user.address,
    role_id: props.user.role_id,
});

const submit = () => {
    Inertia.put(route("users.update", props.user.id), form.value);
};
</script>
