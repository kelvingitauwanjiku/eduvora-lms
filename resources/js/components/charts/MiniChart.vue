<template>
    <svg class="h-full w-full" :viewBox="`0 0 ${width} ${height}`" preserveAspectRatio="none">
        <defs>
            <linearGradient :id="`gradient-${uid}`" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" :stop-color="color" stop-opacity="0.3" />
                <stop offset="100%" :stop-color="color" stop-opacity="0" />
            </linearGradient>
        </defs>
        
        <path 
            :d="areaPath" 
            :fill="`url(#gradient-${uid})`"
            class="transition-all duration-500"
        />
        
        <path 
            :d="linePath" 
            fill="none" 
            :stroke="color" 
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="transition-all duration-500"
        />
        
        <circle 
            v-if="showDot && data.length"
            :cx="lastPoint.x" 
            :cy="lastPoint.y" 
            :r="4" 
            :fill="color"
            class="transition-all duration-300"
        />
        
        <circle 
            v-if="showDot && data.length"
            :cx="lastPoint.x" 
            :cy="lastPoint.y" 
            :r="4" 
            fill="white"
            :opacity="0.3"
            class="transition-all duration-300"
        />
    </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Array, default: () => [] },
    color: { type: String, default: '#3b82f6' },
    showDot: { type: Boolean, default: true },
});

const width = 120;
const height = 40;
const uid = Math.random().toString(36).substr(2, 9);

const normalizeData = computed(() => {
    if (!props.data.length) return [];
    const min = Math.min(...props.data);
    const max = Math.max(...props.data);
    const range = max - min || 1;
    return props.data.map((val, i) => ({
        x: (i / (props.data.length - 1)) * width,
        y: height - ((val - min) / range) * (height - 8),
    }));
});

const linePath = computed(() => {
    if (!normalizeData.value.length) return '';
    return normalizeData.value
        .map((point, i) => `${i === 0 ? 'M' : 'L'} ${point.x} ${point.y}`)
        .join(' ');
});

const areaPath = computed(() => {
    if (!normalizeData.value.length) return '';
    const line = linePath.value;
    const lastX = normalizeData.value[normalizeData.value.length - 1]?.x || width;
    return `${line} L ${lastX} ${height} L 0 ${height} Z`;
});

const lastPoint = computed(() => {
    if (!normalizeData.value.length) return { x: 0, y: 0 };
    return normalizeData.value[normalizeData.value.length - 1];
});
</script>