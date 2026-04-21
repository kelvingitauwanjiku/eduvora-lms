<template>
  <nav class="flex items-center gap-1" aria-label="Pagination">
    <button
      :disabled="current <= 1"
      class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      @click="$emit('change', current - 1)"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
    
    <template v-if="showingAll">
      <button
        v-for="page in totalPages"
        :key="page"
        :class="[
          'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
          page === current
            ? 'bg-blue-600 text-white'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'
        ]"
        @click="$emit('change', page)"
      >
        {{ page }}
      </button>
    </template>
    
    <template v-else>
      <button
        v-for="page in pages"
        :key="page"
        :class="[
          'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
          page === current
            ? 'bg-blue-600 text-white'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'
        ]"
        @click="$emit('change', page)"
      >
        {{ page }}
      </button>
    </template>
    
    <button
      :disabled="current >= totalPages"
      class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      @click="$emit('change', current + 1)"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </nav>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  current: {
    type: Number,
    default: 1
  },
  total: {
    type: Number,
    default: 1
  },
  maxVisible: {
    type: Number,
    default: 5
  }
});

defineEmits(['change']);

const totalPages = computed(() => props.total);
const showingAll = computed(() => props.total <= props.maxVisible);

const pages = computed(() => {
  const total = props.total;
  const current = props.current;
  const maxVisible = props.maxVisible;
  
  if (total <= maxVisible) {
    return Array.from({ length: total }, (_, i) => i + 1);
  }
  
  let start = Math.max(1, current - Math.floor(maxVisible / 2));
  let end = Math.min(total, start + maxVisible - 1);
  
  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});
</script>