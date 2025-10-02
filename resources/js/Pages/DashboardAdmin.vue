<template>
    <AppLayout>
        <div class="min-h-screen bg-slate-950">
            <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 pb-24">
                <div class="max-w-7xl mx-auto px-6 pt-10">
                    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                        <div class="space-y-2">
                            <p class="text-blue-200 text-sm uppercase tracking-widest">Panel del administrador</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold text-white">{{ company.name }}</h1>
                            <p class="text-sm text-blue-200">Gestiona la cuenta, usuarios y configuración corporativa desde un único lugar.</p>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <NavLink :href="route('companies.edit', company.id)" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                                <EditIcon class="w-4 h-4" /> Editar compañía
                            </NavLink>
                            <NavLink :href="route('email-configurations.edit', emailConfig.id)" v-if="emailConfig" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow ring-1 ring-white/20 hover:bg-white/20 transition">
                                <EditIcon class="w-4 h-4" /> Editar correo
                            </NavLink>
                        </div>
                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-10">
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-blue-200">Usuarios</p>
                            <p class="text-3xl font-semibold mt-2">{{ totalUsers }}</p>
                            <p class="text-sm text-blue-200 mt-3">{{ activeRoles }} roles disponibles</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-blue-200">Plan actual</p>
                            <p class="text-3xl font-semibold mt-2">{{ plan.name }}</p>
                            <p class="text-sm text-blue-200 mt-3">{{ plan.description }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-blue-200">Características activas</p>
                            <p class="text-3xl font-semibold mt-2">{{ features.length }}</p>
                            <p class="text-sm text-blue-200 mt-3">{{ highlightedFeature }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur p-6 text-white shadow-lg">
                            <p class="text-xs uppercase tracking-[0.3em] text-blue-200">Estado de correo</p>
                            <p class="text-3xl font-semibold mt-2">{{ emailConfig ? 'Configurado' : 'Pendiente' }}</p>
                            <p class="text-sm text-blue-200 mt-3">{{ emailConfig ? emailConfig.from_email : 'Sin configuración SMTP' }}</p>
                        </div>

                    </div>
                </div>
            </div>
            <div v-else class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg text-blue-500 font-semibold">Configuración de Correo</h2>
                </div>
                <p class="text-gray-700">No se ha configurado ningún servidor de correo todavía.</p>
            </div>

            <div class="max-w-7xl mx-auto px-6 -mt-16 pb-16 space-y-10">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div class="space-y-8 xl:col-span-2">
                        <div class="bg-white rounded-3xl shadow-xl p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-800">Resumen corporativo</h2>
                                    <p class="text-sm text-slate-500 mt-1">Información clave de la empresa y del plan contratado.</p>
                                </div>
                                <NavLink :href="route('company.changePlan', company)" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <EditIcon class="w-4 h-4" /> Gestionar plan
                                </NavLink>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6">
                                <div class="space-y-3 text-sm text-slate-600">
                                    <div class="rounded-2xl border border-slate-200/80 p-4">
                                        <p class="text-xs uppercase tracking-widest text-slate-400">Datos de la empresa</p>
                                        <p class="font-semibold text-slate-700 mt-2">{{ company.name }}</p>
                                        <p class="mt-1">NIF: <span class="font-medium">{{ company.nif }}</span></p>
                                        <p class="mt-1">Teléfono: <span class="font-medium">{{ company.phone }}</span></p>
                                        <p class="mt-1">Dirección: <span class="font-medium">{{ company.address }}</span></p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200/80 p-4">
                                        <p class="text-xs uppercase tracking-widest text-slate-400">Claves API</p>
                                        <p class="mt-2 text-xs text-slate-500 break-all">Public: {{ company.public_key ?? 'No generada' }}</p>
                                        <p class="mt-2 text-xs text-slate-500 break-all">Private: {{ company.private_key ?? 'No generada' }}</p>
                                        <NavLink :href="route('companies.showKeys', company.id)" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700 transition">
                                            Gestionar claves
                                        </NavLink>
                                    </div>
                                </div>
                                <div class="space-y-3 text-sm text-slate-600">
                                    <div class="rounded-2xl border border-slate-200/80 p-4">
                                        <p class="text-xs uppercase tracking-widest text-slate-400">Plan actual</p>
                                        <p class="font-semibold text-slate-700 mt-2">{{ plan.name }}</p>
                                        <p class="text-slate-500 text-sm mt-1">{{ plan.description }}</p>
                                        <p class="text-lg font-semibold text-slate-800 mt-4">{{ plan.price }} €/mes</p>
                                        <ul class="mt-3 space-y-2 text-xs text-slate-500">
                                            <li v-for="feature in displayedFeatures" :key="feature.id" class="flex items-center gap-2">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>{{ feature.name }}
                                            </li>
                                            <li v-if="features.length > 4" class="text-blue-600 font-medium">+ {{ features.length - 4 }} características más</li>
                                        </ul>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200/80 p-4">
                                        <p class="text-xs uppercase tracking-widest text-slate-400">Correo transaccional</p>
                                        <p class="mt-2">Remitente: <span class="font-medium">{{ emailConfig?.from_name ?? 'Sin definir' }}</span></p>
                                        <p class="mt-1">SMTP host: <span class="font-medium">{{ emailConfig?.smtp_host ?? '—' }}</span></p>
                                        <p class="mt-1">Puerto: <span class="font-medium">{{ emailConfig?.smtp_port ?? '—' }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-xl p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-100 pb-5">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-800">Usuarios y actividad</h2>
                                    <p class="text-sm text-slate-500 mt-1">Evolución de la base de usuarios y distribución por roles.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6">
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-400 mb-3">Usuarios por rol</p>
                                    <PieChart :data="usersByRoleChart" />
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-400 mb-3">Altas mensuales</p>
                                    <BarChart :data="usersByMonthChart" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white rounded-3xl shadow-xl p-6">
                            <h2 class="text-xl font-semibold text-slate-800">Roles disponibles</h2>
                            <p class="text-sm text-slate-500">Organiza los permisos del equipo y asigna accesos clave.</p>
                            <ul class="mt-4 space-y-3 text-sm text-slate-600 max-h-72 overflow-y-auto pr-2">
                                <li v-for="role in roles" :key="role.id" class="flex items-start gap-3 rounded-2xl border border-slate-200/80 p-3">
                                    <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ role.name }}</p>
                                        <p class="text-xs text-slate-500">Usuarios asignados: {{ usersAssignedToRole(role.id) }}</p>
                                    </div>
                                </li>
                                <li v-if="roles.length === 0" class="text-sm text-slate-400">Aún no se han definido roles específicos.</li>
                            </ul>
                        </div>

                        <div class="bg-white rounded-3xl shadow-xl p-6 space-y-4">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">Acciones rápidas</h2>
                                <p class="text-sm text-slate-500">Simplifica la gestión del equipo y la compañía.</p>
                            </div>
                            <div class="grid grid-cols-1 gap-3 text-sm">
                                <NavLink :href="route('users.create')" class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    Invitar nuevo usuario
                                    <AddIcon class="w-4 h-4" />
                                </NavLink>
                                <NavLink :href="route('roles.create')" class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    Crear rol personalizado
                                    <AddIcon class="w-4 h-4" />
                                </NavLink>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-xl p-6 space-y-4">
                            <div>
                                <h2 class="text-xl font-semibold text-rose-600">Dar de baja la empresa</h2>
                                <p class="text-sm text-slate-500">Esta acción elimina todos los datos asociados a la compañía de forma irreversible.</p>
                            </div>
                            <button @click="deleteCompany" class="w-full rounded-2xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-rose-700 transition">Eliminar empresa</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import EditIcon from "@/Components/Icons/EditIcon.vue";
import AddIcon from "@/Components/Icons/AddIcon.vue";
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';

const props = defineProps({
    company: Object,
    users: Array,
    plan: Object,
    features: Array,
    roles: Array,
    emailConfig: Object
});

const totalUsers = computed(() => props.users.length);
const activeRoles = computed(() => props.roles.length);
const highlightedFeature = computed(() => props.features[0]?.name ?? 'Sin características registradas');
const displayedFeatures = computed(() => props.features.slice(0, 4));

const usersByRoleChart = computed(() => {
    const roleCounts = props.roles.reduce((acc, role) => {
        acc[role.name] = props.users.filter(user => user.role_id === role.id).length;
        return acc;
    }, {});


    if (props.users.length && Object.keys(roleCounts).length === 0) {
        roleCounts['Sin rol asignado'] = props.users.length;
    }

    return {
        labels: Object.keys(roleCounts),
        datasets: [
            {
                label: 'Usuarios',
                backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#F97316', '#EC4899'],
                data: Object.values(roleCounts),
            },
        ],
    };
});

const usersByMonthChart = computed(() => {
    const grouped = props.users.reduce((acc, user) => {
        if (!user.created_at) {
            return acc;
        }
        const date = new Date(user.created_at);
        const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        acc[key] = (acc[key] || 0) + 1;
        return acc;
    }, {});

    const sortedKeys = Object.keys(grouped).sort();
    return {
        labels: sortedKeys.map(key => {
            const [year, month] = key.split('-');
            return new Date(parseInt(year), parseInt(month) - 1).toLocaleString('default', { month: 'short', year: 'numeric' });
        }),
        datasets: [
            {
                label: 'Altas de usuarios',
                backgroundColor: 'rgba(59, 130, 246, 0.45)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
                data: sortedKeys.map(key => grouped[key]),
            },
        ],
    };
});

function usersAssignedToRole(roleId) {
    return props.users.filter(user => user.role_id === roleId).length;
}

const deleteCompany = () => {
    if (confirm("¿Estás seguro de que deseas eliminar esta compañía?")) {
        Inertia.delete(route('companies.destroy', props.company.id));
    }
};
</script>
