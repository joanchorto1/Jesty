<template>
    <section class="space-y-4 rounded-3xl border border-slate-200 bg-white/70 p-6 shadow-sm">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Estado de recurrencia</h2>
                <p class="text-sm text-slate-500">
                    {{ statusMessage }}
                </p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold" :class="statusClasses">
                <span class="h-2 w-2 rounded-full bg-current"></span>
                {{ statusLabel }}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 text-sm text-slate-600 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Próxima ejecución</p>
                <p class="mt-1 font-semibold text-slate-800">{{ formatDate(template?.next_run_at) ?? 'Pendiente' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Primera emisión</p>
                <p class="mt-1 font-semibold text-slate-800">{{ formatDate(template?.first_issue_on) ?? 'Sin definir' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Frecuencia</p>
                <p class="mt-1 font-semibold text-slate-800">{{ frequencySummary }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Fin programado</p>
                <p class="mt-1 font-semibold text-slate-800">{{ formatDate(template?.ends_at) ?? 'Sin fecha' }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <PrimaryButton
                v-if="template && template.status === 'active'"
                type="button"
                class="bg-amber-500 hover:bg-amber-600"
                @click="updateStatus('paused')"
                :disabled="processing"
            >
                Pausar recurrencia
            </PrimaryButton>
            <PrimaryButton
                v-else-if="template && template.status === 'paused'"
                type="button"
                class="bg-emerald-600 hover:bg-emerald-700"
                @click="updateStatus('active')"
                :disabled="processing"
            >
                Reanudar recurrencia
            </PrimaryButton>
            <span v-if="template && template.status === 'completed'" class="text-sm text-slate-500">
                Plantilla completada automáticamente al alcanzar la fecha de fin.
            </span>
            <span v-if="template && template.status === 'archived'" class="text-sm text-slate-500">
                Plantilla archivada. Puedes duplicarla desde el listado de recurrencias.
            </span>
            <span v-if="!template" class="text-sm text-slate-500">
                Esta factura no forma parte de una plantilla recurrente.
            </span>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    template: {
        type: Object,
        default: null,
    },
});

const processing = ref(false);

const statusLabel = computed(() => {
    if (!props.template) return 'Sin configurar';
    switch (props.template.status) {
        case 'active':
            return 'Activa';
        case 'paused':
            return 'Pausada';
        case 'completed':
            return 'Completada';
        case 'archived':
            return 'Archivada';
        default:
            return 'Borrador';
    }
});

const statusMessage = computed(() => {
    if (!props.template) {
        return 'Esta factura no forma parte de una recurrencia.';
    }
    if (props.template.status === 'active') {
        return 'La plantilla generará automáticamente nuevas facturas según la programación definida.';
    }
    if (props.template.status === 'paused') {
        return 'La generación automática está en pausa hasta que la reanudes manualmente.';
    }
    if (props.template.status === 'completed') {
        return 'Se han emitido todas las recurrencias previstas para esta plantilla.';
    }
    if (props.template.status === 'archived') {
        return 'La plantilla está archivada y no generará más documentos.';
    }
    return 'La plantilla está en borrador y no generará facturas hasta activarse.';
});

const statusClasses = computed(() => {
    if (!props.template) {
        return 'bg-slate-200 text-slate-600';
    }

    switch (props.template.status) {
        case 'active':
            return 'bg-emerald-100 text-emerald-700';
        case 'paused':
            return 'bg-amber-100 text-amber-700';
        case 'completed':
            return 'bg-indigo-100 text-indigo-700';
        case 'archived':
            return 'bg-slate-200 text-slate-600';
        default:
            return 'bg-slate-200 text-slate-600';
    }
});

const frequencySummary = computed(() => {
    if (!props.template) {
        return 'No definida';
    }
    const unitMap = {
        day: 'días',
        week: 'semanas',
        month: 'meses',
        year: 'años',
    };

    const unit = unitMap[props.template.frequency_unit] || props.template.frequency_unit;
    return `Cada ${props.template.frequency_interval} ${unit}`;
});

const formatDate = (value) => {
    if (!value) {
        return null;
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return null;
    }
    return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' });
};

const updateStatus = (status) => {
    if (!props.template) {
        return;
    }
    processing.value = true;
    Inertia.patch(route('recurring-invoices.status', props.template.id), { status }, {
        preserveScroll: true,
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>
