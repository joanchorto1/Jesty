<template>
    <AppLayout>
    <div>
        <h1>Actividades</h1>
        <inertia-link :href="route('activities.create')" class="btn btn-primary">Crear Actividad</inertia-link>
        <table>
            <thead>
            <tr>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="activity in activities.data" :key="activity.id">
                <td>{{ activity.type }}</td>
                <td>{{ activity.date }}</td>
                <td>{{ activity.description }}</td>
                <td>
                    <inertia-link :href="route('activities.show', activity.id)" class="btn btn-info">Ver</inertia-link>
                    <inertia-link :href="route('activities.edit', activity.id)" class="btn btn-warning">Editar</inertia-link>
                    <button @click="deleteActivity(activity.id)" class="btn btn-danger">Eliminar</button>
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
        activities: Object,
    },
    methods: {
        deleteActivity(id) {
            if (confirm('¿Estás seguro de eliminar esta actividad?')) {
                Inertia.delete(route('activities.destroy', id));
            }
        },
    },
};
</script>
