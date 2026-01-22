<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="space-y-2">
                        <p class="text-emerald-100 text-sm uppercase tracking-widest">Jesty · Tareas</p>
                        <h1 class="text-3xl sm:text-4xl font-semibold text-white">Detalle de la tarea</h1>
                        <p class="text-sm text-emerald-100 max-w-2xl">Consulta la información actualizada y gestiona el seguimiento desde Jesty.</p>
                    </div>
                    <NavLink :href="route('user_tasks.index')" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-emerald-500/20 px-4 py-2 text-sm font-semibold text-white backdrop-blur transition hover:bg-emerald-500/30">
                        Volver al listado
                    </NavLink>
                </div>
            </template>

            <AdminPanel title="Información general">
                <div class="space-y-4 text-sm text-slate-600">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-slate-400">Título</p>
                        <p class="mt-1 text-base font-semibold text-slate-800">{{ task.title }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-slate-400">Descripción</p>
                        <p class="mt-1 text-slate-600">{{ task.description || 'Sin descripción' }}</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-slate-400">Fecha de finalización</p>
                            <p class="mt-1 text-slate-600">{{ task.due_date ?? 'Sin definir' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-slate-400">Estado</p>
                            <p class="mt-1 inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ task.status ?? 'Sin estado' }}</p>
                        </div>
                    </div>
                </div>
                <template #actions>
                    <NavLink :href="route('user_tasks.edit', task.id)" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700 transition">
                        Editar tarea
                    </NavLink>
                </template>
            </AdminPanel>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";

defineProps({
    task: Object,
});
</script>
