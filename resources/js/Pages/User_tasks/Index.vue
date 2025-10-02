<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <p class="text-blue-200 text-sm uppercase tracking-widest">Tareas</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Gestión de tareas</h1>
                        <p class="text-sm text-blue-200">Controla el flujo de trabajo del equipo y consulta el estado de las asignaciones.</p>
                    </div>
                    <NavLink :href="route('user_tasks.adminCreate')" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 transition hover:bg-white/20">
                        Nueva tarea
                    </NavLink>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                    <AdminStatCard label="Tareas registradas" :value="totalTasks" :description="totalTasks === 1 ? 'Tarea activa' : 'Tareas activas'" />
                    <AdminStatCard label="Usuarios involucrados" :value="assignedUsers" description="Personas con tareas" />
                    <AdminStatCard label="Con fecha límite" :value="tasksWithDueDate" description="Tareas planificadas" />
                    <AdminStatCard label="Estados distintos" :value="distinctStatuses" description="Tipos de seguimiento" />
                </div>
            </template>

            <AdminPanel title="Listado de tareas" description="Revisa los detalles y aplica acciones rápidas sobre cada registro.">
                <AdminTable>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3">Tarea</th>
                            <th scope="col" class="px-6 py-3">Descripción</th>
                            <th scope="col" class="px-6 py-3">Fecha límite</th>
                            <th scope="col" class="px-6 py-3">Estado</th>
                            <th scope="col" class="px-6 py-3">Asignado a</th>
                            <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="task in tasks" :key="task.id" class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 text-sm font-medium text-slate-700">{{ task.title }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">{{ task.description }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ task.due_date ?? 'Sin fecha' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ task.status ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ assignedUser(task.user_id) }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3 text-slate-500">
                                    <NavLink :href="route('user_tasks.adminEdit', task.id)" class="transition hover:text-amber-500">
                                        <EditIcon class="w-5 h-5" />
                                    </NavLink>
                                    <button @click="deleteTask(task.id)" class="transition hover:text-rose-500">
                                        <DeleteIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="tasks.length === 0">
                            <td colspan="6" class="px-6 py-6 text-center text-sm text-slate-500">No hay tareas registradas por el momento.</td>
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
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";
import AdminStatCard from "@/Components/Dashboard/AdminStatCard.vue";
import AdminTable from "@/Components/Dashboard/AdminTable.vue";

const props = defineProps({
    tasks: Array,
    users: Array,
});

const totalTasks = computed(() => props.tasks.length);
const assignedUsers = computed(() => new Set(props.tasks.map((task) => task.user_id).filter(Boolean)).size);
const tasksWithDueDate = computed(() => props.tasks.filter((task) => Boolean(task.due_date)).length);
const distinctStatuses = computed(() => new Set(props.tasks.map((task) => task.status ?? 'Sin estado')).size);

const usersById = computed(() => Object.fromEntries(props.users.map((user) => [user.id, user.name])));
const assignedUser = (userId) => usersById.value[userId] ?? 'Sin asignar';

const deleteTask = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
        Inertia.delete(route('user_tasks.destroy', id));
    }
};
</script>
