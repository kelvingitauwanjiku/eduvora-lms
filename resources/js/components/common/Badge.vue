<template>
  <span 
    :class="[
      'inline-flex items-center gap-1 rounded-full font-medium',
      sizeClasses,
      variantClasses[variant]
    ]"
  >
    <span v-if="dot" class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => [
      'default', 'primary', 'success', 'warning', 'danger', 'info', 'gray'
    ].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
  },
  dot: {
    type: Boolean,
    default: false
  }
});

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'px-1.5 py-0.5 text-[10px]',
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-sm',
    lg: 'px-3 py-1.5 text-base'
  };
  return sizes[props.size];
});

const variantClasses = computed(() => {
  const variants = {
    default: 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
    primary: 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
    success: 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
    warning: 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
    danger: 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
    info: 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
    gray: 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200'
  };
  return variants[props.variant];
});
</script>