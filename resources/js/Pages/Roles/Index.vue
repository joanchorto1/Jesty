<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Gestión de roles</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Roles y permisos</h1>
                        <p class="text-sm text-blue-200">Organiza los niveles de acceso y asigna responsabilidades a tu equipo.</p>
                    </div>
                    <NavLink
                        :href="route('roles.create')"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 transition hover:bg-white/20"
                    >
                        <AddIcon class="w-4 h-4" /> Nuevo rol
                    </NavLink>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                    <AdminStatCard label="Roles creados" :value="totalRoles" :description="totalRoles === 1 ? 'Rol disponible' : 'Roles disponibles'" />
                    <AdminStatCard label="Roles sin descripción" :value="missingDescription" description="Completa la información para orientar a tu equipo" />
                    <AdminStatCard label="Roles protegidos" :value="protectedRoles" description="No eliminables" />
                    <AdminStatCard label="Roles editables" :value="editableRoles" description="Puedes ajustar sus permisos" />
                </div>
            </template>

            <AdminPanel title="Listado de roles" description="Consulta cada rol definido y gestiona sus acciones disponibles.">
                <AdminTable>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre</th>
                            <th scope="col" class="px-6 py-3">Descripción</th>
                            <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="role in roles" :key="role.id" class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ role.name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ role.description || 'Sin descripción' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3 text-slate-500">
                                    <NavLink :href="route('roles.edit', role.id)" class="transition hover:text-amber-500">
                                        <EditIcon class="w-5 h-5" />
                                    </NavLink>
                                    <button
                                        v-if="role.name !== 'Administrador'"
                                        @click="deleteRole(role.id)"
                                        class="transition hover:text-rose-500"
                                    >
                                        <DeleteIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="roles.length === 0">
                            <td colspan="3" class="px-6 py-6 text-center text-sm text-slate-500">Aún no se han configurado roles.</td>
                        </tr>
                    </tbody>
                </AdminTable>
            </AdminPanel>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";
import AdminStatCard from "@/Components/Dashboard/AdminStatCard.vue";
import AdminTable from "@/Components/Dashboard/AdminTable.vue";

const props = defineProps({
    roles: Array,
});

const totalRoles = computed(() => props.roles.length);
const missingDescription = computed(() => props.roles.filter(role => !role.description).length);
const protectedRoles = computed(() => props.roles.filter(role => role.name === 'Administrador').length);
const editableRoles = computed(() => Math.max(totalRoles.value - protectedRoles.value, 0));

const deleteRole = (roleId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este rol?")) {
        Inertia.delete(route('roles.destroy', roleId));
    }
};
</script>
