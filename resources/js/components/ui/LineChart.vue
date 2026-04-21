<template>
    <div class="relative">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const props = defineProps({
    labels: { type: Array, default: () => [] },
    data: { type: Array, default: () => [] },
    label: { type: String, default: 'Data' },
    color: { type: String, default: '#3b82f6' },
});

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{
        label: props.label,
        data: props.data,
        borderColor: props.color,
        backgroundColor: props.color + '20',
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointHoverRadius: 6,
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1f2937',
            padding: 12,
            cornerRadius: 8,
        }
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { color: '#6b7280' }
        },
        y: {
            grid: { color: '#e5e7eb' },
            ticks: { color: '#6b7280' }
        }
    }
};
</script>