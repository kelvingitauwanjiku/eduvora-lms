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
        
        <div class="flex items-center gap-8">
            <div class="relative" :style="{ width: `${size}px`, height: `${size}px` }">
                <Doughnut :data="chartData" :options="chartOptions" />
            </div>
            
            <div v-if="showLegend" class="flex-1 space-y-3">
                <div 
                    v-for="(item, index) in legendData" 
                    :key="index"
                    class="flex items-center justify-between"
                >
                    <div class="flex items-center gap-3">
                        <div 
                            class="h-3 w-3 rounded-full" 
                            :style="{ backgroundColor: item.color }"
                        ></div>
                        <span class="text-sm text-slate-300">{{ item.label }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-slate-100">{{ item.value }}</span>
                        <span class="text-xs text-slate-500">{{ item.percent }}%</span>
                    </div>
                </div>
            </div>
        </div>
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
    colors: { type: Array, default: () => [] },
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    size: { type: Number, default: 200 },
    showLegend: { type: Boolean, default: true },
    cutout: { type: Number, default: 65 },
});

const defaultColors = [
    '#60a5fa',
    '#34d399',
    '#f472b6',
    '#fbbf24',
    '#a78bfa',
    '#22d3ee',
    '#fb7185',
    '#818cf8',
];

const chartColors = computed(() => props.colors.length ? props.colors : defaultColors);

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{
        data: props.data,
        backgroundColor: chartColors.value.slice(0, props.data.length),
        borderColor: '#1e293b',
        borderWidth: 3,
        hoverOffset: 8,
    }]
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: `${props.cutout}%`,
    plugins: {
        legend: { display: false },
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
    }
}));

const legendData = computed(() => {
    const total = props.data.reduce((a, b) => a + b, 0);
    return props.labels.map((label, i) => ({
        label,
        value: props.data[i],
        percent: total > 0 ? Math.round((props.data[i] / total) * 100) : 0,
        color: chartColors.value[i],
    }));
});
</script>