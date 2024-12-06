<template>
    <div>
        <canvas ref="barChart"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart } from 'chart.js/auto';

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const barChart = ref(null);
let chartInstance = null;

onMounted(() => {
    renderChart();
});

watch(props.data, () => {
    if (chartInstance) {
        chartInstance.destroy();
    }
    renderChart();
});

function renderChart() {
    chartInstance = new Chart(barChart.value, {
        type: 'bar',
        data: props.data,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
}
</script>

<style scoped>
canvas {
    max-width: 100%;
    height: 300px;
}
</style>
