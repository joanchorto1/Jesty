<template>
    <AppLayout>
        <div class="w-full min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-6 rounded-lg shadow-md max-w-3xl mx-auto">
                <h1 class="text-2xl font-bold text-blue-500 mb-4">Crear Tarea</h1>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="title">Título</label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full border border-gray-300 rounded-md p-2"
                            placeholder="Título de la tarea"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2" for="description">Descripción</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="w-full border border-gray-300 rounded-md p-2"
                            placeholder="Descripción de la tarea (opcional)"
                            rows="4"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="due_date">Fecha límite</label>
                            <input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                class="w-full border border-gray-300 rounded-md p-2"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="status">Estado</label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full border border-gray-300 rounded-md p-2"
                                required
                            >
                                <option v-for="statusOption in props.statusOptions" :key="statusOption" :value="statusOption">
                                    {{ capitalise(statusOption) }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="taskable_type">Tipo relacionado</label>
                            <select
                                id="taskable_type"
                                v-model="form.taskable_type"
                                class="w-full border border-gray-300 rounded-md p-2"
                                required
                            >
                                <option value="" disabled>Selecciona un tipo</option>
                                <option v-for="typeOption in typeOptions" :key="typeOption.value" :value="typeOption.value">
                                    {{ typeOption.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-2" for="taskable_id">Relacionado con</label>
                            <select
                                id="taskable_id"
                                v-model="form.taskable_id"
                                class="w-full border border-gray-300 rounded-md p-2"
                                :disabled="!availableOptions.length"
                                required
                            >
                                <option value="" disabled>
                                    {{ availableOptions.length ? 'Selecciona un registro' : 'No hay registros disponibles' }}
                                </option>
                                <option v-for="option in availableOptions" :key="option.id" :value="option.id">
                                    {{ option.label }}

                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button
                            type="button"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
                            @click="cancel"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                        >
                            Guardar

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';


const props = defineProps({
    leads: { type: Array, default: () => [] },
    opportunities: { type: Array, default: () => [] },
    clients: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    typeOptions: { type: Array, default: () => [] },
    statusOptions: { type: Array, default: () => ['pendiente', 'en progreso', 'completada'] },
    prefillTaskable: { type: Object, default: null },
});

const typeOptions = computed(() => props.typeOptions);

const groupedOptions = computed(() => ({
    lead: props.leads,
    opportunity: props.opportunities,
    client: props.clients,
    project: props.projects,
}));

const defaultType = computed(() => {
    if (props.prefillTaskable?.type) {
        return props.prefillTaskable.type;
    }

    return typeOptions.value.length ? typeOptions.value[0].value : '';
});

const form = ref({
    title: '',
    description: '',
    due_date: '',
    status: props.statusOptions[0] ?? 'pendiente',
    taskable_type: defaultType.value,
    taskable_id: props.prefillTaskable?.id ?? '',
});

const availableOptions = computed(() => groupedOptions.value[form.value.taskable_type] ?? []);

watch(
    () => form.value.taskable_type,
    () => {
        if (!availableOptions.value.find((option) => option.id === form.value.taskable_id)) {
            form.value.taskable_id = '';
        }
    }
);

onMounted(() => {
    if (props.prefillTaskable?.type && props.prefillTaskable?.id) {
        form.value.taskable_type = props.prefillTaskable.type;
        form.value.taskable_id = props.prefillTaskable.id;
    }
});

const submit = () => {
    Inertia.post(route('tasks.store'), form.value);
};

const cancel = () => {
    Inertia.visit(route('tasks.index'));

};

const capitalise = (value) => (value ? value.charAt(0).toUpperCase() + value.slice(1) : '');

</script>
