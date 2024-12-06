<template>
    <AppLayout>
    <div>
        <h1>Crear Tarea</h1>
        <form @submit.prevent="submit">
            <input v-model="form.title" type="text" placeholder="Título" required />
            <textarea v-model="form.description" placeholder="Descripción (opcional)"></textarea>
            <input v-model="form.due_date" type="date" required />
            <select v-model="form.status">
                <option value="pendiente">Pendiente</option>
                <option value="en progreso">En Progreso</option>
                <option value="completada">Completada</option>
            </select>
            <input v-model="form.taskable_type" type="text" placeholder="Tipo Relacionado (Lead, Opportunity, Activity)" required />
            <input v-model="form.taskable_id" type="number" placeholder="ID relacionado" required />
            <input v-model="form.company_id" type="number" placeholder="ID de la compañía" required />
            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </div>
    </AppLayout>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
export default {
    components: {AppLayout},
    setup() {
        const form = ref({
            title: '',
            description: '',
            due_date: '',
            status: 'pendiente',
            taskable_type: '',
            taskable_id: '',
            company_id: '',
        });

        const submit = () => {
            Inertia.post(route('tasks.store'), form.value);
        };

        return { form, submit };
    },
};
</script>
