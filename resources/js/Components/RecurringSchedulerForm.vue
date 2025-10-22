<template>
    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-6 space-y-6">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Programación recurrente</h3>
                <p class="text-sm text-slate-500">Activa la recurrencia y define la frecuencia de generación automática.</p>
            </div>
            <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700">
                <input
                    type="checkbox"
                    class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                    :checked="state.enabled"
                    @change="toggleEnabled"
                />
                <span>{{ state.enabled ? 'Recurrencia activada' : 'Recurrencia desactivada' }}</span>
            </label>
        </header>

        <transition name="fade" mode="out-in">
            <div v-if="state.enabled" key="scheduler" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <InputLabel value="Frecuencia" />
                    <SelectInput v-model="state.frequency_type" class="mt-2 block w-full">
                        <option value="">Selecciona una frecuencia</option>
                        <option v-for="option in frequencyOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </SelectInput>
                    <InputError :message="errors['recurring.frequency_type']" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Intervalo" />
                    <TextInput
                        v-model.number="state.frequency_interval"
                        type="number"
                        min="1"
                        class="mt-2 block w-full"
                    />
                    <p class="mt-1 text-xs text-slate-500">Número de intervalos entre ejecuciones.</p>
                    <InputError :message="errors['recurring.frequency_interval']" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Próxima ejecución" />
                    <TextInput v-model="state.next_run_at" type="date" class="mt-2 block w-full" />
                    <InputError :message="errors['recurring.next_run_at']" class="mt-2" />
                </div>

                <div>
                    <InputLabel value="Finaliza el" />
                    <TextInput v-model="state.ends_at" type="date" class="mt-2 block w-full" />
                    <InputError :message="errors['recurring.ends_at']" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-600">
                        <input
                            type="checkbox"
                            class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                            :checked="state.status === 'paused'"
                            @change="togglePause"
                        />
                        <span>{{ statusLabel }}</span>
                    </label>
                    <InputError :message="errors['recurring.status']" class="mt-2" />
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { computed, reactive, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            enabled: false,
        }),
    },
    frequencyOptions: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['update:modelValue']);

const defaultState = () => ({
    enabled: false,
    frequency_type: 'monthly',
    frequency_interval: 1,
    next_run_at: new Date().toISOString().split('T')[0],
    ends_at: '',
    status: 'active',
    id: null,
});

const state = reactive({
    ...defaultState(),
    ...props.modelValue,
});

const syncState = (value) => {
    Object.assign(state, {
        ...defaultState(),
        ...value,
    });
};

watch(
    () => props.modelValue,
    (value) => {
        if (value) {
            syncState(value);
        }
    },
    { deep: true }
);

watch(
    state,
    (value) => {
        emit('update:modelValue', { ...value });
    },
    { deep: true }
);

const toggleEnabled = () => {
    state.enabled = !state.enabled;
    if (!state.enabled) {
        state.status = 'inactive';
    } else if (state.status === 'inactive') {
        state.status = 'active';
    }
};

const togglePause = () => {
    state.status = state.status === 'paused' ? 'active' : 'paused';
    if (state.status === 'active' && !state.next_run_at) {
        state.next_run_at = new Date().toISOString().split('T')[0];
    }
};

const statusLabel = computed(() => {
    if (state.status === 'paused') {
        return 'Recurrencia en pausa';
    }

    if (state.status === 'inactive') {
        return 'Recurrencia inactiva';
    }

    return 'Recurrencia activa';
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
