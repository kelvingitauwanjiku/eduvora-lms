<template>
    <div class="relative">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const props = defineProps({
    labels: { type: Array, default: () => [] },
    data: { type: Array, default: () => [] },
    label: { type: String, default: 'Data' },
    color: { type: String, default: '#3b82f6' },
    horizontal: { type: Boolean, default: false },
});

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{
        label: props.label,
        data: props.data,
        backgroundColor: props.color,
        borderRadius: 6,
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: props.horizontal ? 'y' : 'x',
    plugins: {
        legend: { display: false },
        tooltip: { backgroundColor: '#1f2937', padding: 12, cornerRadius: 8 }
    },
    scales: {
        x: { grid: { display: false }, ticks: { color: '#6b7280' } },
        y: { grid: { color: '#e5e7eb' }, ticks: { color: '#6b7280' } }
    }
};
</script>