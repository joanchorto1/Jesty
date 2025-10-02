<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="space-y-3">
                    <p class="text-blue-200 text-sm uppercase tracking-widest">Tareas</p>
                    <h1 class="text-3xl sm:text-4xl font-semibold text-white">Editar tarea</h1>
                    <p class="text-sm text-blue-200 max-w-2xl">Actualiza los datos y mantiene informados a los responsables.</p>
                </div>
            </template>

            <AdminPanel title="Detalle de la tarea" description="Modifica solo los campos necesarios antes de guardar.">
                <form @submit.prevent="updateTask" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600">Título</label>
                            <input v-model="form.title" type="text" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600">Descripción</label>
                            <textarea v-model="form.description" rows="4" :class="[inputClasses, 'min-h-[120px]']" required></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Fecha de finalización</label>
                            <input v-model="form.due_date" type="date" :class="inputClasses" required />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600">Asignar a</label>
                            <select v-model="form.user_id" :class="inputClasses" required>
                                <option value="" disabled>Selecciona un usuario</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <NavLink :href="route('user_tasks.index')" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </AdminPanel>
        </AdminPage>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import AdminPage from "@/Components/Dashboard/AdminPage.vue";
import AdminPanel from "@/Components/Dashboard/AdminPanel.vue";

const props = defineProps({
    task: Object,
    users: Array,
});

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = reactive({
    title: props.task.title,
    description: props.task.description,
    due_date: props.task.due_date,
    user_id: props.task.user_id,
});

const updateTask = () => {
    Inertia.put(route('user_tasks.adminUpdate', props.task.id), form);
};
</script>
