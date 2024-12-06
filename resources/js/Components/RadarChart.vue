<template>
    <div>
        <canvas ref="radarChart"></canvas>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js';

// Importamos los elementos necesarios de Chart.js
import { Radar } from 'chart.js';
import { Chart as ChartJS } from 'chart.js';
import { Title, Tooltip, Legend, RadialLinearScale, PointElement, LineElement } from 'chart.js';

ChartJS.register(
    RadialLinearScale,   // Escala radial para el gráfico de radar
    PointElement,        // Elemento para los puntos
    LineElement,         // Elemento para las líneas
    Title,               // Título
    Tooltip,             // Tooltip
    Legend               // Leyenda
);

const props = defineProps({
    data: Object,
});

const radarChart = ref(null);

onMounted(() => {
    createChart();
});

watch(() => props.data, () => {
    createChart();
}, { immediate: true });

const createChart = () => {
    if (radarChart.value) {
        new Chart(radarChart.value, {
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
    }
};
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
