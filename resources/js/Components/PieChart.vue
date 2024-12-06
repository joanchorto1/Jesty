<template>
    <div>
        <canvas ref="pieChart"></canvas>
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

const pieChart = ref(null);
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
    chartInstance = new Chart(pieChart.value, {
        type: 'pie',
        data: props.data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
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

