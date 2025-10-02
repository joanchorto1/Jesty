<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="space-y-3">
                    <p class="text-blue-200 text-sm uppercase tracking-widest">Tareas</p>
                    <h1 class="text-3xl sm:text-4xl font-semibold text-white">Editar tarea personal</h1>
                    <p class="text-sm text-blue-200 max-w-2xl">Revisa y actualiza el progreso de tu tarea.</p>
                </div>
            </template>

            <AdminPanel title="Información básica" description="Modifica la tarea y guarda los cambios.">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-600" for="title">Título</label>
                        <input id="title" v-model="form.title" type="text" :class="inputClasses" required />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-600" for="description">Descripción</label>
                        <textarea id="description" v-model="form.description" rows="4" :class="[inputClasses, 'min-h-[120px]']"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-600" for="due_date">Fecha de finalización</label>
                        <input id="due_date" v-model="form.due_date" type="date" :class="inputClasses" required />
                    </div>
                    <div class="flex justify-end gap-3">
                        <NavLink :href="route('user_tasks.index')" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</NavLink>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                            Actualizar tarea
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
});

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = reactive({
    title: props.task.title,
    description: props.task.description,
    due_date: props.task.due_date,
});

const submit = () => {
    Inertia.put(route('user_tasks.update', props.task.id), form);
};
</script>
