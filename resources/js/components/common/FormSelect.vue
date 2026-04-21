<template>
  <div class="relative inline-block" :class="customClass">
    <select
      v-model="modelValue"
      :disabled="disabled"
      :class="[
        'block w-full rounded-lg transition-all duration-200 appearance-none pr-10',
        'border-2 bg-transparent focus:ring-2 focus:ring-offset-2 focus:outline-none',
        sizeClasses,
        error 
          ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20' 
          : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500/20',
        disabled ? 'opacity-50 cursor-not-allowed' : ''
      ]"
      @change="handleChange"
    >
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    
    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  customClass: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'pl-3 pr-8 py-1.5 text-sm',
    md: 'pl-4 pr-10 py-2.5 text-sm',
    lg: 'pl-4 pr-10 py-3 text-base'
  };
  return sizes[props.size];
});

const handleChange = (e) => {
  emit('update:modelValue', e.target.value);
  emit('change', e.target.value);
};
</script>