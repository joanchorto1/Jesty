<template>
    <div>
        <canvas ref="canvas"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import Line from 'chart.js/auto';

// Props que reciben los datos y opciones del gráfico
const props = defineProps({
    data: Object,
    options: Object,
});

// Referencia al elemento canvas para renderizar el gráfico
const canvas = ref(null);
let chartInstance = null;

// Función para inicializar el gráfico
const initializeChart = () => {
    if (chartInstance) {
        chartInstance.destroy(); // Destruir la instancia anterior si existe
    }

    chartInstance = new Line(canvas.value, {
        type: 'line',
        data: props.data,
        options: props.options,
    });
};

// Re-renderiza el gráfico si cambian los datos o las opciones
watch(() => props.data, initializeChart);
watch(() => props.options, initializeChart);

// Monta el gráfico al cargar el componente
onMounted(initializeChart);
</script>

<style scoped>
canvas {
    max-width: 100%;
    height: 400px;
}
</style>
