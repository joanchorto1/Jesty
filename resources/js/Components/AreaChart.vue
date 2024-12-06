<template>
    <div>
        <canvas ref="areaChart"></canvas>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { Chart } from 'chart.js';
import { Chart as ChartJS } from 'chart.js';

// Registro de tipos de gráficos que vamos a utilizar
import { CategoryScale, LinearScale, PointElement, LineElement, Filler, Title, Tooltip, Legend } from 'chart.js';
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Title, Tooltip, Legend);

const props = defineProps({
    data: Object,
});

const areaChart = ref(null);

onMounted(() => {
    createChart();
});

watch(() => props.data, () => {
    createChart();
}, { immediate: true });

const createChart = () => {
    if (areaChart.value) {
        new Chart(areaChart.value, {
            type: 'line',
            data: props.data,
            options: {
                responsive: true,
                scales: {
                    x: { type: 'category', labels: props.data.labels },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad',
                        },
                    },
                },
                elements: {
                    line: {
                        tension: 0.4, // Curvatura de la línea (para el área)
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
