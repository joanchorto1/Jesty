<template>
    <AppLayout>
    <div class="max-w-lg mx-auto mt-10 p-8 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Editar Tarea</h2>

        <form @submit.prevent="updateTask">
            <div class="mb-4">
                <label for="title" class="block text-gray-600 font-medium">Título</label>
                <input type="text" id="title" v-model="form.title" class="w-full p-2 border rounded-lg" required />
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-600 font-medium">Descripción</label>
                <textarea id="description" v-model="form.description" class="w-full p-2 border rounded-lg" required></textarea>
            </div>

            <div class="mb-4">
                <label for="due_date" class="block text-gray-600 font-medium">Fecha de Finalización</label>
                <input type="date" id="due_date" v-model="form.due_date" class="w-full p-2 border rounded-lg" required />
            </div>



            <div class="mb-4">
                <label for="user" class="block text-gray-600 font-medium">Asignar a Usuario</label>
                <select id="user" v-model="form.user_id" class="w-full p-2 border rounded-lg">
                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 font-medium">Proyecto asociado</label>
                <select v-model="form.project_id" class="w-full p-2 border rounded-lg">
                    <option :value="null">Sin proyecto</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Actualizar Tarea</button>
        </form>
    </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {ref} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    task: Object,
    users: Array,
    projects: { type: Array, default: () => [] },
});

const form = ref({
    title: props.task.title,
    description: props.task.description,
    due_date: props.task.due_date,
    user_id: props.task.user_id,
    project_id: props.task.project_id,
});

function updateTask() {
    Inertia.put(route('user_tasks.adminUpdate', props.task.id), form.value);
}

</script>

<style scoped>
input, textarea, select {
    outline: none;
}
</style>
