<template>
  <div 
    :class="[
      'bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700',
      'transition-all duration-200',
      hoverable ? 'hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 cursor-pointer' : 'shadow-sm',
      padding ? paddingClasses[padding] : 'p-6',
      customClass
    ]"
    @click="$emit('click', $event)"
  >
    <div v-if="$slots.header" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 -m-6 mb-4">
      <slot name="header" />
    </div>
    
    <slot />
    
    <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 -m-6 mt-4 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  padding: {
    type: String,
    default: 'md',
    validator: (value) => ['', 'none', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  hoverable: {
    type: Boolean,
    default: false
  },
  customClass: {
    type: String,
    default: ''
  }
});

defineEmits(['click']);

const paddingClasses = {
  none: '',
  sm: 'p-4',
  md: 'p-6',
  lg: 'p-8',
  xl: 'p-10'
};
</script>