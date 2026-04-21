<template>
  <div v-if="loading" class="flex items-center justify-center py-12">
    <LoadingSpinner :size="loadingSize" :text="loadingText" />
  </div>
  
  <div v-else-if="empty" class="py-12 text-center">
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
      <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
    </div>
    <h3 v-if="emptyTitle" class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
      {{ emptyTitle }}
    </h3>
    <p v-if="emptyText" class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
      {{ emptyText }}
    </p>
    <slot name="empty-actions" />
  </div>
  
  <div v-else>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-700">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
          <tr>
            <th 
              v-for="column in columns" 
              :key="column.key"
              class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
              :class="column.align === 'right' ? 'text-right' : column.align === 'center' ? 'text-center' : 'text-left'"
              :style="column.width ? { width: column.width } : {}"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>
        
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <tr 
            v-for="(row, index) in data" 
            :key="row[rowKey] || index"
            class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
            @click="$emit('row-click', row)"
            :class="{ 'cursor-pointer': rowClickable }"
          >
            <td 
              v-for="column in columns" 
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap"
              :class="[
                column.align === 'right' ? 'text-right' : column.align === 'center' ? 'text-center' : 'text-left',
                column.class || 'text-sm text-gray-900 dark:text-gray-100'
              ]"
            >
              <slot :name="`cell-${column.key}`" :row="row" :column="column" :value="row[column.key]">
                {{ row[column.key] }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div v-if="paginated" class="mt-4 flex items-center justify-between">
      <div class="text-sm text-gray-500 dark:text-gray-400">
        Showing {{ startIndex + 1 }} to {{ Math.min(endIndex + 1, total) }} of {{ total }} results
      </div>
      
      <Pagination 
        :current="currentPage" 
        :total="totalPages" 
        @change="handlePageChange"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import LoadingSpinner from './LoadingSpinner.vue';
import Pagination from './Pagination.vue';

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  data: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  empty: {
    type: Boolean,
    default: false
  },
  emptyTitle: {
    type: String,
    default: 'No data available'
  },
  emptyText: {
    type: String,
    default: ''
  },
  loadingText: {
    type: String,
    default: ''
  },
  loadingSize: {
    type: String,
    default: 'md'
  },
  rowKey: {
    type: String,
    default: 'id'
  },
  rowClickable: {
    type: Boolean,
    default: false
  },
  paginated: {
    type: Boolean,
    default: false
  },
  currentPage: {
    type: Number,
    default: 1
  },
  perPage: {
    type: Number,
    default: 10
  },
  total: {
    type: Number,
    default: 0
  }
});

const emit = defineEmits(['row-click', 'page-change']);

const totalPages = computed(() => Math.ceil(props.total / props.perPage));
const startIndex = computed(() => (props.currentPage - 1) * props.perPage);
const endIndex = computed(() => startIndex.value + props.perPage - 1);

const handlePageChange = (page) => {
  emit('page-change', page);
};
</script>