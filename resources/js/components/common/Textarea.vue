<template>
  <div class="relative">
    <label v-if="label" :for="textareaId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <textarea
      :id="textareaId"
      v-model="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      :required="required"
      :rows="rows"
      :maxlength="maxlength"
      :class="[
        'block w-full rounded-xl transition-all duration-200 resize-none',
        'border-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm',
        'focus:ring-2 focus:ring-offset-2 focus:outline-none',
        'disabled:cursor-not-allowed disabled:opacity-50',
        'dark:text-white dark:placeholder-gray-400',
        'px-4 py-3',
        error 
          ? 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500/20' 
          : 'border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500/20'
      ]"
      @input="handleInput"
      @blur="emit('blur', $event)"
      @focus="emit('focus', $event)"
    ></textarea>
    
    <div v-if="maxlength && showCount" class="mt-1 flex justify-end">
      <span class="text-xs text-gray-400">
        {{ modelValue?.length || 0 }} / {{ maxlength }}
      </span>
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
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
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
  readonly: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  rows: {
    type: Number,
    default: 4
  },
  maxlength: {
    type: Number,
    default: 0
  },
  showCount: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'input']);

const { generateId } = useGenerateId();
const textareaId = computed(() => props.label ? generateId(props.label) : 'textarea');

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
  emit('input', event.target.value);
};
</script>