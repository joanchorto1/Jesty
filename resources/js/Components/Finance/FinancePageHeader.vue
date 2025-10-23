<template>
    <div class="bg-gradient-to-r from-slate-900 via-emerald-700 to-blue-900 pb-24 print:bg-white print:pb-10">
        <div class="max-w-7xl mx-auto px-6 pt-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-2 text-white print:text-slate-900">
                    <p v-if="eyebrow" class="text-emerald-200 text-xs uppercase tracking-[0.35em] print:text-emerald-700">
                        {{ eyebrow }}
                    </p>
                    <h1 class="text-3xl sm:text-4xl font-semibold">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-emerald-200 max-w-2xl print:text-slate-600">
                        {{ description }}
                    </p>
                </div>
                <div v-if="$slots.actions" class="flex flex-wrap gap-3 print:hidden">
                    <slot name="actions" />
                </div>
            </div>

            <div v-if="$slots.metrics" class="mt-10">
                <div :class="metricsGridClass">
                    <slot name="metrics" />
                </div>
            </div>

            <div
                v-if="showPeriodSelector"
                class="mt-10 flex flex-col gap-4 rounded-2xl border border-white/20 bg-white/10 p-4 text-white/80 shadow-lg shadow-emerald-900/20 backdrop-blur print:hidden"
            >
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="text-xs font-semibold uppercase tracking-widest text-emerald-200/80">
                        Selecciona periodo de análisis
                    </div>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <button
                            v-for="option in periodOptions"
                            :key="option.value"
                            type="button"
                            class="rounded-full border border-white/20 px-4 py-1.5 font-medium transition"
                            :class="{
                                'bg-white/90 text-emerald-700 shadow': option.value === periodMode,
                                'hover:bg-white/20': option.value !== periodMode,
                            }"
                            :aria-pressed="option.value === periodMode"
                            @click="selectPeriodMode(option.value)"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </div>

                <div
                    v-if="canPickYear"
                    class="flex flex-col gap-3 border-t border-white/10 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between"
                >
                    <label class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-emerald-200/80">Año</span>
                        <select
                            class="rounded-xl border border-white/30 bg-white/10 px-3 py-2 text-sm font-medium text-white focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/40"
                            :value="selectedYear ?? (availableYears[0] ?? '')"
                            @change="onYearChange"
                        >
                            <option v-for="year in availableYears" :key="year" :value="year">
                                {{ year }}
                            </option>
                        </select>
                    </label>

                    <label v-if="canPickMonth" class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-emerald-200/80">Mes</span>
                        <select
                            class="rounded-xl border border-white/30 bg-white/10 px-3 py-2 text-sm font-medium text-white focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/40"
                            :value="selectedMonth ?? 1"
                            @change="onMonthChange"
                        >
                            <option
                                v-for="(monthLabel, index) in monthLabels"
                                :key="monthLabel"
                                :value="index + 1"
                            >
                                {{ monthLabel }}
                            </option>
                        </select>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    eyebrow: {
        type: String,
        default: '',
    },
    metricsColumns: {
        type: Number,
        default: 4,
    },
    showPeriodSelector: {
        type: Boolean,
        default: false,
    },
    periodMode: {
        type: String,
        default: 'general',
    },
    availableYears: {
        type: Array,
        default: () => [],
    },
    selectedYear: {
        type: Number,
        default: null,
    },
    selectedMonth: {
        type: Number,
        default: null,
    },
    monthNames: {
        type: Array,
        default: () => [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ],
    },
});

const emit = defineEmits(['update:periodMode', 'update:selectedYear', 'update:selectedMonth']);

const metricsGridClass = computed(() => {
    const base = 'grid grid-cols-1 gap-5';
    const variants = {
        1: `${base}`,
        2: `${base} sm:grid-cols-2`,
        3: `${base} sm:grid-cols-2 xl:grid-cols-3`,
        4: `${base} sm:grid-cols-2 xl:grid-cols-4`,
        5: `${base} sm:grid-cols-2 xl:grid-cols-5`,
    };

    return variants[props.metricsColumns] ?? variants[4];
});

const periodOptions = [
    { value: 'monthly', label: 'Mensual' },
    { value: 'annual', label: 'Anual' },
    { value: 'general', label: 'General' },
];

const canPickYear = computed(() => props.periodMode !== 'general' && props.availableYears.length > 0);
const canPickMonth = computed(() => props.periodMode === 'monthly');
const monthLabels = computed(() => props.monthNames.length ? props.monthNames : periodOptions.map(option => option.label));

const selectPeriodMode = (value) => {
    if (value !== props.periodMode) {
        emit('update:periodMode', value);
    }
};

const onYearChange = (event) => {
    const value = Number(event.target.value);
    if (!Number.isNaN(value)) {
        emit('update:selectedYear', value);
    }
};

const onMonthChange = (event) => {
    const value = Number(event.target.value);
    if (!Number.isNaN(value)) {
        emit('update:selectedMonth', value);
    }
};
</script>
