<template>
    <form @submit.prevent="$emit('submit')" class="space-y-8">
        <div
            v-if="form.hasErrors"
            class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700"
            role="alert"
        >
            <p class="font-semibold">No se pudo guardar la oportunidad. Revisa los campos con errores.</p>
        </div>

        <div v-if="showLeadSelect" class="bg-white/5 rounded-2xl border border-white/10 px-5 py-4 text-sm text-slate-300">
            <p class="font-semibold text-slate-100">Asigna un lead</p>
            <p class="mt-1 text-xs uppercase tracking-[0.3em] text-slate-500">Cada oportunidad necesita un lead asociado</p>
        </div>

        <div v-if="disableSubmit" class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-6 text-center text-slate-300">
            <p class="text-sm">No hay leads disponibles. Crea un lead antes de registrar la oportunidad.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="opportunity-description" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Descripción</label>
                <textarea
                    id="opportunity-description"
                    v-model="form.description"
                    rows="4"
                    placeholder="Describe el reto, necesidad u objetivo del cliente"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('description')"
                    :aria-describedby="hasError('description') ? 'opportunity-description-error' : undefined"
                ></textarea>
                <p v-if="hasError('description')" :id="'opportunity-description-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.description }}</p>
            </div>

            <div>
                <label for="opportunity-value" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Valor estimado (€)</label>
                <div class="mt-2 flex items-center gap-2 rounded-2xl border border-white/20 bg-white/5 px-4 py-3">
                    <span class="text-sm text-slate-400">€</span>
                    <input
                        id="opportunity-value"
                        v-model.number="form.value"
                        type="number"
                        min="0"
                        step="0.01"
                        required
                        placeholder="0,00"
                        class="w-full bg-transparent text-sm text-white placeholder:text-slate-400 focus:outline-none"
                        :aria-invalid="hasError('value')"
                        :aria-describedby="hasError('value') ? 'opportunity-value-error' : undefined"
                    />
                </div>
                <p v-if="hasError('value')" :id="'opportunity-value-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.value }}</p>
            </div>

            <div>
                <label for="opportunity-status" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Estado</label>
                <select
                    id="opportunity-status"
                    v-model="form.status"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('status')"
                    :aria-describedby="hasError('status') ? 'opportunity-status-error' : undefined"
                >
                    <option v-for="statusOption in statuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
                </select>
                <p v-if="hasError('status')" :id="'opportunity-status-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.status }}</p>
            </div>

            <div class="md:col-span-2">
                <label for="opportunity-probability" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Probabilidad de cierre</label>
                <div class="mt-3 space-y-3">
                    <div class="flex items-center gap-4">
                        <input
                            id="opportunity-probability"
                            v-model.number="form.probability"
                            type="range"
                            min="0"
                            max="100"
                            class="w-full accent-violet-500"
                            :aria-valuenow="Number(form.probability ?? 0)"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        />
                        <span class="inline-flex min-w-[4rem] items-center justify-center rounded-full bg-violet-500/20 px-3 py-1 text-sm font-semibold text-violet-200">{{ Number(form.probability ?? 0) }}%</span>
                    </div>
                    <input
                        v-model.number="form.probability"
                        type="number"
                        min="0"
                        max="100"
                        class="w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-slate-400 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                        placeholder="Introduce un valor porcentual"
                        :aria-invalid="hasError('probability')"
                        :aria-describedby="hasError('probability') ? 'opportunity-probability-error' : undefined"
                    />
                </div>
                <p v-if="hasError('probability')" :id="'opportunity-probability-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.probability }}</p>
            </div>

            <div v-if="showLeadSelect" class="md:col-span-2">
                <label for="opportunity-lead" class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Lead asociado</label>
                <select
                    id="opportunity-lead"
                    v-model="form.lead_id"
                    :disabled="disableSubmit"
                    class="mt-2 w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white disabled:cursor-not-allowed disabled:opacity-60 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200"
                    :aria-invalid="hasError('lead_id')"
                    :aria-describedby="hasError('lead_id') ? 'opportunity-lead-error' : undefined"
                >
                    <option value="" disabled>Selecciona un lead</option>
                    <option v-for="lead in leads" :key="lead.id" :value="lead.id">{{ lead.name }}</option>
                </select>
                <p v-if="hasError('lead_id')" :id="'opportunity-lead-error'" class="mt-2 text-sm text-rose-300">{{ form.errors.lead_id }}</p>
            </div>

            <div v-else class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-200 uppercase tracking-widest">Lead asociado</label>
                <div class="mt-2 flex items-center justify-between rounded-2xl border border-white/20 bg-white/5 px-4 py-3 text-sm text-white">
                    <span>{{ selectedLeadName }}</span>
                    <span class="text-xs uppercase tracking-[0.3em] text-slate-500">Asignado</span>
                </div>
                <input type="hidden" v-model="form.lead_id" />
            </div>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 text-xs uppercase tracking-[0.3em] text-slate-400">
                <span>Atajos</span>
                <span aria-hidden="true">•</span>
                <span>Ctrl + Enter para guardar</span>
            </div>
            <div class="flex flex-wrap gap-3">
                <button
                    v-if="cancelHref"
                    type="button"
                    class="inline-flex items-center justify-center rounded-2xl border border-white/20 px-4 py-2 text-sm font-semibold text-white/80 hover:bg-white/10 transition"
                    @click="$emit('cancel', cancelHref)"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-violet-500 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-violet-500/30 hover:bg-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-200 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:cursor-not-allowed disabled:opacity-70"
                    :disabled="processing || disableSubmit"
                >
                    <span v-if="processing" class="flex items-center gap-2">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/60 border-t-white"></span>
                        Guardando
                    </span>
                    <span v-else>{{ submitLabel }}</span>
                </button>
            </div>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    leads: {
        type: Array,
        default: () => [],
    },
    processing: {
        type: Boolean,
        default: false,
    },
    submitLabel: {
        type: String,
        default: 'Guardar oportunidad',
    },
    statuses: {
        type: Array,
        default: () => ['En proceso', 'Ganada', 'Perdida'],
    },
    cancelHref: {
        type: String,
        default: null,
    },
    showLeadSelect: {
        type: Boolean,
        default: true,
    },
    selectedLeadName: {
        type: String,
        default: '',
    },
});

const disableSubmit = computed(() => props.showLeadSelect && props.leads.length === 0);

function hasError(field) {
    return Boolean(props.form.errors?.[field]);
}
</script>
