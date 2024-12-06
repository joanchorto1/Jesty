<template>
    <AppLayout>
    <div>
        <h1>Notas</h1>
        <inertia-link :href="route('notes.create')" class="btn btn-primary">Crear Nota</inertia-link>
        <table>
            <thead>
            <tr>
                <th>Contenido</th>
                <th>Tipo Relacionado</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="note in notes.data" :key="note.id">
                <td>{{ note.content }}</td>
                <td>{{ note.noteable_type }}</td>
                <td>
                    <inertia-link :href="route('notes.show', note.id)" class="btn btn-info">Ver</inertia-link>
                    <inertia-link :href="route('notes.edit', note.id)" class="btn btn-warning">Editar</inertia-link>
                    <button @click="deleteNote(note.id)" class="btn btn-danger">Eliminar</button>
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
        notes: Object,
    },
    methods: {
        deleteNote(id) {
            if (confirm('¿Estás seguro de eliminar esta nota?')) {
                Inertia.delete(route('notes.destroy', id));
            }
        },
    },
};
</script>
