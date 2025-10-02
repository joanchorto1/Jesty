<template>
    <AppLayout>
        <AdminPage>
            <template #header>
                <div class="space-y-3">
                    <p class="text-blue-200 text-sm uppercase tracking-widest">Tareas</p>
                    <h1 class="text-3xl sm:text-4xl font-semibold text-white">Crear tarea personal</h1>
                    <p class="text-sm text-blue-200 max-w-2xl">Registra una nueva tarea para organizar tu trabajo pendiente.</p>
                </div>
            </template>

            <AdminPanel title="Detalles de la tarea" description="Completa el formulario con la información básica.">
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
                            Crear tarea
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

const inputClasses = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none transition';

const form = reactive({
    title: '',
    description: '',
    due_date: '',
});

const submit = () => {
    Inertia.post(route('user_tasks.store'), form);
};
</script>
