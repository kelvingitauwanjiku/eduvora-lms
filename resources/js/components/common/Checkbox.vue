<template>
  <label 
    class="relative flex items-start cursor-pointer group"
    :class="{ 'cursor-not-allowed opacity-50': disabled }"
  >
    <div class="flex items-center h-5">
      <input
        :id="checkboxId"
        v-model="modelValue"
        :type="type"
        :name="name"
        :value="value"
        :disabled="disabled"
        :required="required"
        :checked="isChecked"
        :indeterminate="indeterminate"
        :class="[
          'w-5 h-5 rounded-lg border-2 transition-all duration-200',
          'focus:ring-2 focus:ring-offset-2 focus:outline-none',
          'disabled:cursor-not-allowed',
          modelValue || isChecked || indeterminate
            ? 'bg-blue-600 border-blue-600 dark:bg-blue-500 dark:border-blue-500'
            : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600',
          error ? 'border-red-500' : ''
        ]"
        @change="handleChange"
      />
    </div>
    
    <div class="ml-3 text-sm">
      <label 
        v-if="label" 
        :for="checkboxId"
        class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer"
        :class="{ 'text-gray-500 dark:text-gray-400': disabled }"
      >
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      
      <p v-if="description" class="text-gray-500 dark:text-gray-400 mt-0.5">
        {{ description }}
      </p>
      
      <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ error }}
      </p>
    </div>
  </label>
</template>

<script setup>
import { computed } from 'vue';
import { useGenerateId } from '../../composables/useGenerateId';

const props = defineProps({
  modelValue: {
    type: [Boolean, Array],
    default: false
  },
  type: {
    type: String,
    default: 'checkbox'
  },
  name: {
    type: String,
    default: ''
  },
  value: {
    type: [String, Number, Boolean],
    default: true
  },
  label: {
    type: String,
    default: ''
  },
  description: {
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
  indeterminate: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const { generateId } = useGenerateId();
const checkboxId = computed(() => props.label ? generateId(props.label) : 'checkbox');

const isChecked = computed(() => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(props.value);
  }
  return props.modelValue;
});

const handleChange = (event) => {
  let newValue;
  
  if (Array.isArray(props.modelValue)) {
    if (event.target.checked) {
      newValue = [...props.modelValue, props.value];
    } else {
      newValue = props.modelValue.filter(v => v !== props.value);
    }
  } else {
    newValue = event.target.checked;
  }
  
  emit('update:modelValue', newValue);
  emit('change', newValue);
};
</script>