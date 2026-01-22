<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-100/80 py-12">
            <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6">
                <CrudPageHeader
                    :title="`Hola ${user.name}, tu día en Jesty está listo`"
                    description="Panel personal Jesty con todas las prioridades y métricas clave al instante."
                >
                    <template #actions>
                        <NavLink :href="route('user_tasks.create')" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            Crear tarea Jesty
                        </NavLink>
                    </template>
                </CrudPageHeader>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-widest text-emerald-500">Tareas abiertas</p>
                        <p class="text-3xl font-semibold mt-2">{{ openTasks }}</p>
                        <p class="text-sm text-slate-500 mt-3">{{ inProgressTasks }} en progreso • {{ pendingTasks }} pendientes</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-widest text-emerald-500">Entregas de hoy</p>
                        <p class="text-3xl font-semibold mt-2">{{ tasksDueToday }}</p>
                        <p class="text-sm text-slate-500 mt-3">{{ overdueTasks }} atrasadas sin finalizar</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-widest text-emerald-500">Tareas finalizadas</p>
                        <p class="text-3xl font-semibold mt-2">{{ completedTasks }}</p>
                        <p class="text-sm text-slate-500 mt-3">{{ completionRate }}% de avance</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-5 text-slate-800 shadow-sm">
                        <p class="text-xs uppercase tracking-widest text-emerald-500">Alertas</p>
                        <p class="text-3xl font-semibold mt-2">{{ unreadNotifications }}</p>
                        <p class="text-sm text-slate-500 mt-3">Notificaciones pendientes por revisar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-2 space-y-6">
                        <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200/70 pb-5">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-800">Agenda y prioridades Jesty</h2>
                                    <p class="text-sm text-slate-500 mt-1">Visualiza tu calendario y organiza los próximos hitos.</p>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-slate-500">
                                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-sky-500"></span>En curso</div>
                                    <div class="flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>Completadas</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6">
                                <div class="rounded-2xl border border-dashed border-slate-200/80 bg-white/60 p-4">
                                    <Calendar id="calendar" class="w-full" />
                                </div>
                                <div class="flex flex-col">
                                    <h3 class="text-sm font-semibold text-slate-600 mb-3">Próximas entregas</h3>
                                    <ul class="flex-1 space-y-3 overflow-y-auto pr-2">
                                        <li v-for="task in upcomingTasks" :key="`upcoming-${task.id}`" class="flex items-start gap-3 rounded-2xl border border-slate-200/70 bg-white/70 p-4">
                                            <div :class="['mt-1 h-2.5 w-2.5 rounded-full', task.status === 'completed' ? 'bg-emerald-500' : task.status === 'in_progress' ? 'bg-sky-500' : 'bg-amber-500']"></div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-700">{{ task.title }}</p>
                                                <p class="text-xs text-slate-500 mt-1">Entrega {{ formatDate(task.due_date) }}</p>
                                                <p v-if="task.description" class="text-xs text-slate-500 mt-2 line-clamp-2">{{ task.description }}</p>
                                            </div>
                                        </li>
                                        <li v-if="upcomingTasks.length === 0" class="text-sm text-slate-400">No hay tareas programadas para los próximos días.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200/70 pb-5">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-800">Panel de tareas Jesty</h2>
                                    <p class="text-sm text-slate-500 mt-1">Gestiona tus pendientes, cambia estados y mantén el foco.</p>
                                </div>
                            </div>
                            <div class="overflow-x-auto pt-6">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-xs uppercase tracking-wider text-slate-400">
                                            <th class="pb-3">Tarea</th>
                                            <th class="pb-3">Entrega</th>
                                            <th class="pb-3">Estado</th>
                                            <th class="pb-3 text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/70 text-sm text-slate-600">
                                        <tr v-for="task in orderedTasks" :key="task.id" class="hover:bg-slate-50/70 transition">
                                            <td class="py-4">
                                                <p class="font-medium text-slate-700">{{ task.title }}</p>
                                                <p v-if="task.description" class="text-xs text-slate-400 mt-1 line-clamp-2">{{ task.description }}</p>
                                            </td>
                                            <td class="py-4">{{ formatDate(task.due_date) }}</td>
                                            <td class="py-4">
                                                <span :class="statusBadgeClasses(task.status)">{{ statusCopy(task.status) }}</span>
                                            </td>
                                            <td class="py-4">
                                                <div class="flex items-center justify-end gap-3 text-slate-500">
                                                    <NavLink :href="route('user_tasks.show', task.id)" class="hover:text-emerald-500 transition"><InfoIcon class="w-5 h-5" title="Ver detalle"/></NavLink>
                                                    <NavLink :href="route('user_tasks.edit', task.id)" class="hover:text-amber-500 transition"><EditIcon class="w-5 h-5" title="Editar"/></NavLink>
                                                    <NavLink v-if="task.status !== 'completed'" :href="route('user_tasks.mark_as_completed', task.id)" class="hover:text-emerald-500 transition" title="Marcar como finalizada">
                                                        <MenuCategoryIcon class="w-5 h-5" />
                                                    </NavLink>
                                                    <NavLink v-if="task.status !== 'in_progress'" :href="route('user_tasks.mark_as_in_progress', task.id)" class="hover:text-sky-500 transition" title="Marcar como en progreso">
                                                        <MenuProductIcon class="w-5 h-5" />
                                                    </NavLink>
                                                    <button @click="deleteTask(task.id)" class="hover:text-rose-500 transition" title="Eliminar">
                                                        <DeleteIcon class="w-5 h-5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="orderedTasks.length === 0">
                                            <td colspan="4" class="py-6 text-center text-slate-400">Aún no tienes tareas registradas.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-slate-800">Resumen visual Jesty</h2>
                            <p class="text-sm text-slate-500 mb-4">Estado de tus tareas y carga mensual.</p>
                            <div class="space-y-8">
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-400 mb-3">Distribución por estado</p>
                                    <PieChart :data="taskStatusChart" />
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-400 mb-3">Planificación mensual</p>
                                    <BarChart :data="taskMonthlyChart" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200/80 bg-white/80 p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-slate-800">Notificaciones Jesty</h2>
                            <p class="text-sm text-slate-500 mb-4">Actualizaciones relevantes para mantenerte al día.</p>
                            <ul class="space-y-4 max-h-80 overflow-y-auto pr-2">
                                <li v-for="notification in notifications" :key="notification.id" class="rounded-2xl border border-slate-200/70 bg-white/70 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-700">{{ notification.title }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ notification.message }}</p>
                                        </div>
                                        <NavLink :href="route('user-notifications.markAsRead', notification.id)" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition">Marcar como leído</NavLink>
                                    </div>
                                </li>
                                <li v-if="notifications.length === 0" class="text-sm text-slate-400">No tienes notificaciones pendientes.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import Calendar from '@/Components/Calendar.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import NavLink from "../Components/NavLink.vue";
import CrudPageHeader from '@/Components/Crud/CrudPageHeader.vue';
import { Inertia } from "@inertiajs/inertia";
import InfoIcon from "@/Components/Icons/InfoIcon.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import DeleteIcon from "@/Components/Icons/DeleteIcon.vue";
import MenuProductIcon from "@/Components/Icons/MenuProductIcon.vue";
import MenuCategoryIcon from "@/Components/Icons/MenuCategoryIcon.vue";
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';

const props = defineProps({
    user: Object,
    notifications: Array,
    tasks: Array,
    teamChat: Array,
});

const orderedTasks = computed(() => {
    return [...props.tasks].sort((a, b) => {
        const dateA = a.due_date ? new Date(a.due_date).getTime() : Number.POSITIVE_INFINITY;
        const dateB = b.due_date ? new Date(b.due_date).getTime() : Number.POSITIVE_INFINITY;
        return dateA - dateB;
    });
});

const today = new Date();
const tasksDueToday = computed(() => props.tasks.filter(task => isSameDay(task.due_date, today)).length);
const overdueTasks = computed(() => props.tasks.filter(task => isBeforeToday(task.due_date, today) && task.status !== 'completed').length);
const completedTasks = computed(() => props.tasks.filter(task => task.status === 'completed').length);
const inProgressTasks = computed(() => props.tasks.filter(task => task.status === 'in_progress').length);
const pendingTasks = computed(() => props.tasks.filter(task => !task.status || (task.status !== 'completed' && task.status !== 'in_progress')).length);
const openTasks = computed(() => props.tasks.length - completedTasks.value);
const unreadNotifications = computed(() => props.notifications.filter(notification => !notification.read_at).length);

const completionRate = computed(() => {
    if (props.tasks.length === 0) {
        return 0;
    }
    return Math.round((completedTasks.value / props.tasks.length) * 100);
});

const upcomingTasks = computed(() => orderedTasks.value.filter(task => task.due_date && new Date(task.due_date) >= today).slice(0, 6));

const taskStatusChart = computed(() => {
    const counts = props.tasks.reduce((acc, task) => {
        const status = statusCopy(task.status);
        acc[status] = (acc[status] || 0) + 1;
        return acc;
    }, {});

    const labels = Object.keys(counts);
    const data = Object.values(counts);

    return {
        labels: labels.length ? labels : ['Sin tareas'],
        datasets: [
            {
                label: 'Tareas',
                backgroundColor: ['#34D399', '#38BDF8', '#6366F1', '#F59E0B', '#F472B6'],
                data: data.length ? data : [1],
            },
        ],
    };
});

const taskMonthlyChart = computed(() => {
    const monthlyTotals = props.tasks.reduce((acc, task) => {
        if (!task.due_date) {
            return acc;
        }
        const month = new Date(task.due_date).toLocaleString('default', { month: 'short' });
        acc[month] = (acc[month] || 0) + 1;
        return acc;
    }, {});

    return {
        labels: Object.keys(monthlyTotals),
        datasets: [
            {
                label: 'Tareas programadas',
                backgroundColor: 'rgba(16, 185, 129, 0.45)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                data: Object.values(monthlyTotals),
            },
        ],
    };
});

function deleteTask(taskId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
        Inertia.delete(route('user_tasks.destroy', taskId));
    }
}

function formatDate(date) {
    if (!date) {
        return 'Sin fecha';
    }
    return new Date(date).toLocaleDateString();
}

function statusCopy(status) {
    switch (status) {
        case 'completed':
            return 'Completada';
        case 'in_progress':
            return 'En progreso';
        case 'pending':
            return 'Pendiente';
        default:
            return status || 'Sin estado';
    }
}

function statusBadgeClasses(status) {
    switch (status) {
        case 'completed':
            return 'inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
        case 'in_progress':
            return 'inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700';
        case 'pending':
            return 'inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700';
        default:
            return 'inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600';
    }
}

function isSameDay(date, reference) {
    if (!date) {
        return false;
    }
    const target = new Date(date);
    return target.getDate() === reference.getDate() && target.getMonth() === reference.getMonth() && target.getFullYear() === reference.getFullYear();
}

function isBeforeToday(date, reference) {
    if (!date) {
        return false;
    }
    const target = new Date(date);
    return target < new Date(reference.getFullYear(), reference.getMonth(), reference.getDate());
}
</script>

<style scoped>
#calendar {
    max-height: 380px;
    border-radius: 18px;
    overflow: hidden;
}
</style>
