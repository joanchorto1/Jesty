<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Gestión de usuarios</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Usuarios</h1>
                        <p class="text-sm text-blue-200">Administra los miembros del equipo y controla los permisos asignados.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <NavLink
                            v-if="canCreateUser"
                            :href="route('users.create')"
                            class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 transition hover:bg-white/20"
                        >
                            <AddIcon class="w-4 h-4" /> Nuevo usuario
                        </NavLink>
                        <NavLink
                            v-else
                            :href="route('company.changePlan', company)"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-4 py-2 text-sm font-semibold text-white/80 backdrop-blur transition hover:bg-white/10"
                        >
                            <AddIcon class="w-4 h-4" /> Amplía tu plan
                        </NavLink>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                    <AdminStatCard label="Usuarios activos" :value="totalUsers" :description="`${seatsRemaining} lugares disponibles`" />
                    <AdminStatCard label="Roles disponibles" :value="rolesCount" :description="rolesCount === 1 ? 'Rol configurado' : 'Roles configurados'" />
                    <AdminStatCard label="Límite del plan" :value="planLimit" :description="planName" />
                    <AdminStatCard label="Estado" :value="canCreateUser ? 'Con capacidad' : 'Límite alcanzado'">
                        <template #badge>
                            <span :class="statusBadgeClasses">{{ canCreateUser ? 'Disponible' : 'Completo' }}</span>
                        </template>
                    </AdminStatCard>
                </div>
            </template>

            <AdminPanel title="Listado de usuarios" description="Consulta la información clave de cada miembro y gestiona sus acciones.">
                <AdminTable>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Rol</th>
                            <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ user.name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ getUserRole(user.role_id) }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3 text-slate-500">
                                    <NavLink :href="route('users.edit', user.id)" class="transition hover:text-amber-500">
                                        <EditIcon class="w-5 h-5" />
                                    </NavLink>
                                    <button
                                        v-if="getUserRole(user.role_id) !== 'Administrador'"
                                        @click="deleteUser(user.id)"
                                        class="transition hover:text-rose-500"
                                    >
                                        <DeleteIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
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
    users: Array,
    roles: Array,
    plan: Object,
    company: Object,
});

const totalUsers = computed(() => props.users.length);
const rolesCount = computed(() => props.roles.length);
const planLimit = computed(() => props.plan?.user_limit ?? 0);
const planName = computed(() => props.plan?.name ?? 'Plan actual');
const seatsRemaining = computed(() => Math.max(planLimit.value - totalUsers.value, 0));
const canCreateUser = computed(() => totalUsers.value < planLimit.value);

const statusBadgeClasses = computed(() => [
    'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide',
    canCreateUser.value ? 'bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-400/30' : 'bg-rose-500/20 text-rose-300 ring-1 ring-rose-400/30',
]);

const getUserRole = (roleId) => {
    const role = props.roles.find((r) => r.id === roleId);
    return role ? role.name : 'Sin rol';
};

const deleteUser = (userId) => {
    if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
        Inertia.delete(route('users.destroy', userId));
    }
};
</script>
