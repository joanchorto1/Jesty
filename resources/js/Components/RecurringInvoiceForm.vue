<template>
    <section class="space-y-5 rounded-3xl border border-dashed border-slate-300/70 bg-white/60 p-6">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Facturación recurrente</h2>
                <p class="text-sm text-slate-500">
                    Automatiza la emisión periódica generando plantillas con frecuencia personalizada.
                </p>
            </div>
            <label class="inline-flex cursor-pointer items-center gap-3">
                <span class="text-sm font-medium text-slate-600">Activar recurrencia</span>
                <input
                    type="checkbox"
                    class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                    :checked="state.is_recurring"
                    @change="toggleRecurring($event.target.checked)"
                />
            </label>
        </div>

        <transition name="fade">
            <div v-if="state.is_recurring" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="space-y-2">
                    <InputLabel value="Estado de la plantilla" />
                    <SelectInput v-model="state.recurring_status" class="mt-2 block w-full">
                        <option value="draft">Borrador</option>
                        <option value="active">Activa</option>
                        <option value="paused">Pausada</option>
                    </SelectInput>
                </div>
                <div class="space-y-2">
                    <InputLabel value="Estado de las facturas generadas" />
                    <SelectInput v-model="state.recurring_invoice_state" class="mt-2 block w-full">
                        <option value="pending">Pendiente</option>
                        <option value="paid">Pagada</option>
                        <option value="cancelled">Cancelada</option>
                    </SelectInput>
                </div>
                <div class="space-y-2">
                    <InputLabel value="Unidad de frecuencia" />
                    <SelectInput v-model="state.frequency_unit" class="mt-2 block w-full">
                        <option value="day">Diaria</option>
                        <option value="week">Semanal</option>
                        <option value="month">Mensual</option>
                        <option value="year">Anual</option>
                    </SelectInput>
                </div>
                <div class="space-y-2">
                    <InputLabel value="Intervalo" />
                    <TextInput
                        v-model.number="state.frequency_interval"
                        type="number"
                        min="1"
                        class="mt-2 block w-full"
                    />
                </div>
                <div class="space-y-2">
                    <InputLabel value="Primera emisión" />
                    <TextInput v-model="state.first_issue_on" type="date" class="mt-2 block w-full" />
                </div>
                <div class="space-y-2">
                    <InputLabel value="Finaliza el" />
                    <TextInput v-model="state.ends_at" type="date" class="mt-2 block w-full" />
                    <p class="text-xs text-slate-400">Deja este campo vacío para que la recurrencia sea indefinida.</p>
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                            v-model="state.generate_first_invoice"
                        />
                        Generar automáticamente la primera factura en la fecha indicada
                    </label>
                </div>
            </div>
        </transition>
    </section>
</template>

<script setup>
import { reactive, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({}),
    },
    initialDate: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const defaults = {
    is_recurring: false,
    recurring_status: 'active',
    recurring_invoice_state: 'pending',
    frequency_unit: 'month',
    frequency_interval: 1,
    first_issue_on: '',
    ends_at: '',
    generate_first_invoice: true,
};

const state = reactive({ ...defaults, ...props.modelValue });

const syncState = (value) => {
    Object.assign(state, { ...defaults, ...value });
    if (state.is_recurring && !state.first_issue_on && props.initialDate) {
        state.first_issue_on = props.initialDate;
    }
};

watch(
    () => props.modelValue,
    (value) => {
        syncState(value || {});
    },
    { deep: true, immediate: true }
);

watch(
    () => props.initialDate,
    (date) => {
        if (state.is_recurring && !state.first_issue_on && date) {
            state.first_issue_on = date;
        }
    }
);

watch(
    state,
    (value) => {
        emit('update:modelValue', { ...value });
    },
    { deep: true }
);

const toggleRecurring = (enabled) => {
    state.is_recurring = enabled;
    if (enabled) {
        if (!state.first_issue_on && props.initialDate) {
            state.first_issue_on = props.initialDate;
        }
        if (!state.recurring_status) {
            state.recurring_status = 'active';
        }
    }
};
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
