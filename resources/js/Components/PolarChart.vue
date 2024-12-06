<template>
    <div>
        <canvas ref="polarCanvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, PolarAreaController, ArcElement, RadialLinearScale, Tooltip, Legend } from 'chart.js';

// Registrar componentes de Chart.js necesarios
Chart.register(PolarAreaController, ArcElement, RadialLinearScale, Tooltip, Legend);

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const polarCanvas = ref(null);
let chartInstance = null;

onMounted(() => {
    if (polarCanvas.value) {
        chartInstance = new Chart(polarCanvas.value, {
            type: 'polarArea',
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
                scales: {
                    r: {
                        ticks: {
                            display: true,
                        },
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
