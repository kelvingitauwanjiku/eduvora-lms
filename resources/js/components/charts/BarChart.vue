<template>
    <div class="rounded-2xl border border-slate-700/50 bg-slate-900/50 p-6 backdrop-blur-sm dark:border-slate-700/50 dark:bg-slate-800/50">
        <div v-if="title" class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-100">{{ title }}</h3>
                <p v-if="subtitle" class="mt-1 text-sm text-slate-400">{{ subtitle }}</p>
            </div>
            <div v-if="$slots.actions" class="flex items-center gap-2">
                <slot name="actions" />
            </div>
        </div>
        
        <div class="relative" :style="{ height: `${height}px` }">
            <Bar :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const props = defineProps({
    labels: { type: Array, default: () => [] },
    datasets: { type: Array, default: () => [] },
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    height: { type: Number, default: 300 },
    showLegend: { type: Boolean, default: false },
    horizontal: { type: Boolean, default: false },
});

const defaultColors = [
    { bg: 'rgba(96, 165, 250, 0.8)', hover: '#60a5fa' },
    { bg: 'rgba(52, 211, 153, 0.8)', hover: '#34d399' },
    { bg: 'rgba(244, 114, 182, 0.8)', hover: '#f472b6' },
    { bg: 'rgba(251, 191, 36, 0.8)', hover: '#fbbf24' },
    { bg: 'rgba(167, 139, 250, 0.8)', hover: '#a78bfa' },
    { bg: 'rgba(34, 211, 238, 0.8)', hover: '#22d3ee' },
];

const chartData = computed(() => ({
    labels: props.labels,
    datasets: props.datasets.map((dataset, i) => ({
        ...dataset,
        backgroundColor: dataset.backgroundColor || defaultColors[i % defaultColors.length].bg,
        hoverBackgroundColor: dataset.hoverBackgroundColor || defaultColors[i % defaultColors.length].hover,
        borderRadius: 8,
        borderSkipped: false,
        barThickness: props.barThickness || 32,
    })),
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: props.horizontal ? 'y' : 'x',
    plugins: {
        legend: { 
            display: props.showLegend,
            position: 'top',
            align: 'end',
            labels: {
                color: '#94a3b8',
                usePointStyle: true,
                pointStyle: 'circle',
                padding: 20,
                font: { size: 12 }
            }
        },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f1f5f9',
            bodyColor: '#cbd5e1',
            padding: 16,
            cornerRadius: 12,
            borderColor: '#334155',
            borderWidth: 1,
            displayColors: true,
            usePointStyle: true,
        }
    },
    scales: {
        x: {
            grid: { 
                display: props.horizontal,
                drawBorder: false,
            },
            ticks: { 
                color: '#64748b',
                font: { size: 11 },
            },
            border: { display: false },
        },
        y: {
            grid: { 
                display: !props.horizontal,
                color: 'rgba(148, 163, 184, 0.1)',
                drawBorder: false,
            },
            ticks: { 
                color: '#64748b',
                font: { size: 11 },
                padding: 8,
            },
            border: { display: false },
        }
    }
}));
</script>
