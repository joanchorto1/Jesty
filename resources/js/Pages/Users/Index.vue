<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex justify-between items-center">
                <h1 class="text-lg text-blue-500 font-semibold">Usuarios</h1>
                <NavLink
                    :href="route('users.create')"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <AddIcon class="w-5 h-5 inline-block mr-2" /> Nuevo Usuario
                </NavLink>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Rol</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-t">
                        <td class="px-4 py-2">{{ user.name }}</td>
                        <td class="px-4 py-2">{{ user.email }}</td>
                        <td class="px-4 py-2">{{ getUserRole(user.role_id) }}</td>
                        <td class="px-4 py-2">
<!--                            <NavLink :href="route('users.show', user.id)" class="text-blue-500 mr-2">-->
<!--                                <InfoIcon class="w-5 h-5" />-->
<!--                            </NavLink>-->
                            <NavLink :href="route('users.edit', user.id)" class="text-yellow-500 mr-2">
                                <EditIcon class="w-5 h-5" />
                            </NavLink>
                            <button v-if="getUserRole(user.role_id) !== 'Administrador'" @click="deleteUser(user.id)" class="text-red-500">
                                <DeleteIcon class="w-5 h-5" />
                            </button>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    users: Array,
    roles: Array,
    company: Object
});


const getUserRole = (roleId) => {
    const role = props.roles.find((r) => r.id === roleId);
    return role ? role.name : 'Sin Rol';
};

const deleteUser = (userId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
        Inertia.delete(route('users.destroy', userId));
    }
};
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
