<template>
  <div class="relative">
    <label v-if="label" :for="selectId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div class="relative">
      <select
        :id="selectId"
        v-model="modelValue"
        :disabled="disabled"
        :required="required"
        :multiple="multiple"
        :class="[
          'block w-full rounded-xl transition-all duration-200 appearance-none',
          'border-2 bg-white dark:bg-gray-800',
          'focus:ring-2 focus:ring-offset-2 focus:outline-none',
          'disabled:cursor-not-allowed disabled:opacity-50',
          'dark:text-white',
          error 
            ? 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500/20' 
            : 'border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500/20',
          multiple ? 'py-3 pl-4 pr-10 min-h-[120px]' : 'py-2.5 pl-4 pr-10'
        ]"
        @change="handleChange"
      >
        <option v-if="placeholder && !multiple" value="" disabled :selected="!modelValue">
          {{ placeholder }}
        </option>
        <option 
          v-for="option in options" 
          :key="getOptionValue(option)" 
          :value="getOptionValue(option)"
        >
          {{ getOptionLabel(option) }}
        </option>
      </select>
      
      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>
    
    <div v-if="error" class="mt-1.5 flex items-center gap-1">
      <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    </div>
    
    <p v-else-if="hint" class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useGenerateId } from '../../composables/useGenerateId';

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  optionValue: {
    type: String,
    default: 'value'
  },
  optionLabel: {
    type: String,
    default: 'label'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Select an option'
  },
  hint: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  multiple: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const { generateId } = useGenerateId();
const selectId = computed(() => props.label ? generateId(props.label) : 'select');

const getOptionValue = (option) => {
  if (typeof option === 'object') {
    return option[props.optionValue];
  }
  return option;
};

const getOptionLabel = (option) => {
  if (typeof option === 'object') {
    return option[props.optionLabel];
  }
  return option;
};

const handleChange = (event) => {
  emit('update:modelValue', event.target.value);
  emit('change', event.target.value);
};
</script>