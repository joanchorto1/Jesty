<template>
    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/80 p-4">
        <canvas ref="radarChart"></canvas>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    RadialLinearScale,
    PointElement,
    LineElement,
    Filler,
} from 'chart.js';

ChartJS.register(
    RadialLinearScale, // Escala radial para el gráfico de radar
    PointElement, // Elemento para los puntos
    LineElement, // Elemento para las líneas
    Title, // Título
    Tooltip, // Tooltip
    Legend, // Leyenda
    Filler,
);

const props = defineProps({
    data: Object,
});

const radarChart = ref(null);
const chartInstance = ref(null);

onMounted(() => {
    createChart();
});

watch(() => props.data, () => {
    createChart();
}, { immediate: true });

const createChart = () => {
    if (!radarChart.value) {
        return;
    }

    if (chartInstance.value) {
        chartInstance.value.destroy();
    }

    chartInstance.value = new ChartJS(radarChart.value, {
        type: 'radar',
        data: props.data,
        options: {
            responsive: true,
            scales: {
                r: {
                    min: 0,
                    max: 100,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10,
                    },
                },
            },
            elements: {
                line: {
                    borderWidth: 2,
                },
            },
        },
    });
};
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
