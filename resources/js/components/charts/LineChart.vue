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
            <Line :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const props = defineProps({
    labels: { type: Array, default: () => [] },
    datasets: { type: Array, default: () => [] },
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    height: { type: Number, default: 300 },
    showLegend: { type: Boolean, default: false },
});

const defaultColors = [
    { line: '#60a5fa', bg: 'rgba(96, 165, 250, 0.1)' },
    { line: '#34d399', bg: 'rgba(52, 211, 153, 0.1)' },
    { line: '#f472b6', bg: 'rgba(244, 114, 182, 0.1)' },
    { line: '#fbbf24', bg: 'rgba(251, 191, 36, 0.1)' },
    { line: '#a78bfa', bg: 'rgba(167, 139, 250, 0.1)' },
];

const chartData = computed(() => ({
    labels: props.labels,
    datasets: props.datasets.map((dataset, i) => ({
        ...dataset,
        borderColor: dataset.borderColor || defaultColors[i % defaultColors.length].line,
        backgroundColor: dataset.backgroundColor || defaultColors[i % defaultColors.length].bg,
        fill: dataset.fill !== undefined ? dataset.fill : true,
        tension: dataset.tension || 0.4,
        pointRadius: dataset.pointRadius || 0,
        pointHoverRadius: dataset.pointHoverRadius || 6,
        pointBackgroundColor: dataset.borderColor || defaultColors[i % defaultColors.length].line,
        pointBorderColor: '#1e293b',
        pointBorderWidth: 2,
    })),
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        intersect: false,
        mode: 'index',
    },
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
                display: false,
                drawBorder: false,
            },
            ticks: { 
                color: '#64748b',
                font: { size: 11 },
                maxRotation: 0,
            },
            border: { display: false },
        },
        y: {
            grid: { 
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
    },
    elements: {
        line: {
            borderWidth: 2,
        }
    }
}));
</script>
