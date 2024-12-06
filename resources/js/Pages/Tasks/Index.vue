<template>
    <AppLayout>
    <div>
        <h1>Tareas</h1>
        <inertia-link :href="route('tasks.create')" class="btn btn-primary">Crear Tarea</inertia-link>
        <table>
            <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Fecha de Vencimiento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="task in tasks.data" :key="task.id">
                <td>{{ task.title }}</td>
                <td>{{ task.description }}</td>
                <td>{{ task.due_date }}</td>
                <td>{{ task.status }}</td>
                <td>
                    <inertia-link :href="route('tasks.show', task.id)" class="btn btn-info">Ver</inertia-link>
                    <inertia-link :href="route('tasks.edit', task.id)" class="btn btn-warning">Editar</inertia-link>
                    <button @click="deleteTask(task.id)" class="btn btn-danger">Eliminar</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    </AppLayout>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    components: {AppLayout},
    props: {
        tasks: Object,
    },
    methods: {
        deleteTask(id) {
            if (confirm('¿Estás seguro de eliminar esta tarea?')) {
                Inertia.delete(route('tasks.destroy', id));
            }
        },
    },
};
</script>
