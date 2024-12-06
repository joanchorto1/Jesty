<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex justify-between items-center">
                <h1 class="text-lg text-blue-500 font-semibold">Roles</h1>
                <NavLink :href="route('roles.create')" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <AddIcon class="w-5 h-5 inline-block mr-2" /> Nuevo Rol
                </NavLink>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <table class="w-full table-auto">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="role in roles" :key="role.id" class="border-t">
                        <td class="px-4 py-2">{{ role.name }}</td>
                        <td class="px-4 py-2">{{ role.description }}</td>
                        <td class="px-4 py-2">
<!--                            <NavLink :href="route('roles.show', role.id)" class="text-blue-500 mr-2">-->
<!--                                <InfoIcon class="w-5 h-5" />-->
<!--                            </NavLink>-->
                            <NavLink :href="route('roles.edit', role.id)" class="text-yellow-500 mr-2">
                                <EditIcon class="w-5 h-5" />
                            </NavLink>
                            <button v-if="role.name!== 'Administrador'" @click="deleteRole(role.id)" class="text-red-500">
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
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    roles: Array,
});

const deleteRole = (roleId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este rol?")) {
        Inertia.delete(route('roles.destroy', roleId));
    }
};
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
