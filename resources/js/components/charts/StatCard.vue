<template>
    <div 
        class="relative overflow-hidden rounded-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-xl"
        :class="[bgGradient, cardClass]"
    >
        <div class="absolute inset-0 opacity-30" :class="shineClass"></div>
        
        <div class="relative p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div 
                        class="flex h-12 w-12 items-center justify-center rounded-xl"
                        :class="iconBgClass"
                    >
                        <component :is="icon" class="h-6 w-6" :class="iconClass" />
                    </div>
                    <div>
                        <p class="text-sm font-medium opacity-70">{{ title }}</p>
                        <p class="mt-1 text-2xl font-bold">{{ formattedValue }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 flex items-center gap-2">
                <span 
                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                    :class="changeBgClass"
                >
                    <svg 
                        class="h-3 w-3" 
                        :class="change >= 0 ? 'rotate-0' : 'rotate-180'" 
                        fill="currentColor" 
                        viewBox="0 0 12 12"
                    >
                        <path d="M6 2L10 7H2L6 2Z" />
                    </svg>
                    {{ Math.abs(change) }}%
                </span>
                <span class="text-xs opacity-60">vs last period</span>
            </div>
            
            <div v-if="showSparkline" class="mt-4 h-12">
                <MiniChart :data="sparklineData" :color="sparklineColor" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import MiniChart from './MiniChart.vue';

const props = defineProps({
    title: { type: String, required: true },
    value: { type: [Number, String], required: true },
    change: { type: Number, default: 0 },
    icon: { type: Object, required: true },
    variant: { type: String, default: 'blue' },
    format: { type: String, default: 'number' },
    showSparkline: { type: Boolean, default: false },
    sparklineData: { type: Array, default: () => [] },
});

const variants = {
    blue: {
        bgGradient: 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800',
        cardClass: 'border border-slate-700/50',
        shineClass: 'bg-gradient-to-br from-white/5 to-transparent',
        iconBgClass: 'bg-blue-500/20',
        iconClass: 'text-blue-400',
        sparklineColor: '#60a5fa',
    },
    emerald: {
        bgGradient: 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800',
        cardClass: 'border border-slate-700/50',
        shineClass: 'bg-gradient-to-br from-white/5 to-transparent',
        iconBgClass: 'bg-emerald-500/20',
        iconClass: 'text-emerald-400',
        sparklineColor: '#34d399',
    },
    amber: {
        bgGradient: 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800',
        cardClass: 'border border-slate-700/50',
        shineClass: 'bg-gradient-to-br from-white/5 to-transparent',
        iconBgClass: 'bg-amber-500/20',
        iconClass: 'text-amber-400',
        sparklineColor: '#fbbf24',
    },
    rose: {
        bgGradient: 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800',
        cardClass: 'border border-slate-700/50',
        shineClass: 'bg-gradient-to-br from-white/5 to-transparent',
        iconBgClass: 'bg-rose-500/20',
        iconClass: 'text-rose-400',
        sparklineColor: '#fb7185',
    },
    violet: {
        bgGradient: 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800',
        cardClass: 'border border-slate-700/50',
        shineClass: 'bg-gradient-to-br from-white/5 to-transparent',
        iconBgClass: 'bg-violet-500/20',
        iconClass: 'text-violet-400',
        sparklineColor: '#8b5cf6',
    },
    cyan: {
        bgGradient: 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800',
        cardClass: 'border border-slate-700/50',
        shineClass: 'bg-gradient-to-br from-white/5 to-transparent',
        iconBgClass: 'bg-cyan-500/20',
        iconClass: 'text-cyan-400',
        sparklineColor: '#22d3ee',
    },
};

const selectedVariant = computed(() => variants[props.variant] || variants.blue);

const bgGradient = computed(() => selectedVariant.value.bgGradient);
const cardClass = computed(() => selectedVariant.value.cardClass);
const shineClass = computed(() => selectedVariant.value.shineClass);
const iconBgClass = computed(() => selectedVariant.value.iconBgClass);
const iconClass = computed(() => selectedVariant.value.iconClass);
const sparklineColor = computed(() => selectedVariant.value.sparklineColor);

const changeBgClass = computed(() => 
    props.change >= 0 
        ? 'bg-emerald-500/20 text-emerald-400' 
        : 'bg-rose-500/20 text-rose-400'
);

const formattedValue = computed(() => {
    if (typeof props.value === 'string') return props.value;
    if (props.format === 'currency') {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0 }).format(props.value);
    }
    if (props.format === 'percent') return `${props.value}%`;
    if (props.format === 'compact') {
        return new Intl.NumberFormat('en-US', { notation: 'compact', compactDisplay: 'short' }).format(props.value);
    }
    return new Intl.NumberFormat('en-US').format(props.value);
});
</script>