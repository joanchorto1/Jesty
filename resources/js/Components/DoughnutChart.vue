<template>
    <div>
        <canvas ref="doughnutCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, DoughnutController, ArcElement, Tooltip, Legend } from 'chart.js';

// Registrar componentes de Chart.js necesarios
Chart.register(DoughnutController, ArcElement, Tooltip, Legend);

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const doughnutCanvas = ref(null);
let chartInstance = null;

onMounted(() => {
    if (doughnutCanvas.value) {
        chartInstance = new Chart(doughnutCanvas.value, {
            type: 'doughnut',
            data: props.data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        enabled: true,
                    },
                },
            },
        });
    }
});

watch(
    () => props.data,
    (newData) => {
        if (chartInstance) {
            chartInstance.data = newData;
            chartInstance.update();
        }
    },
    { deep: true }
);
</script>

<style scoped>
canvas {
    max-width: 100%;
}
</style>
