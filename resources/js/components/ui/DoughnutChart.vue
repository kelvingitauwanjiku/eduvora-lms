<template>
    <div class="relative">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    labels: { type: Array, default: () => [] },
    data: { type: Array, default: () => [] },
    colors: { type: Array, default: () => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] },
});

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{
        data: props.data,
        backgroundColor: props.colors,
        borderWidth: 0,
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } },
        tooltip: { backgroundColor: '#1f2937', padding: 12, cornerRadius: 8 }
    },
    cutout: '65%',
};
</script>