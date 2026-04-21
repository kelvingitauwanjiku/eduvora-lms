<template>
  <div class="relative">
    <label v-if="label" :for="inputId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div class="relative">
      <div v-if="$slots.prefix" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <slot name="prefix" />
      </div>
      
      <input
        :id="inputId"
        v-model="modelValue"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :autocomplete="autocomplete"
        :class="[
          'block w-full rounded-xl transition-all duration-200',
          'border-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm',
          'focus:ring-2 focus:ring-offset-2 focus:outline-none',
          'disabled:cursor-not-allowed disabled:opacity-50',
          'dark:text-white dark:placeholder-gray-400',
          $slots.prefix ? 'pl-10' : 'pl-4',
          $slots.suffix || iconRight || error ? 'pr-10' : 'pr-4',
          error 
            ? 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500/20' 
            : 'border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-blue-500/20'
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="handleBlur"
        @focus="$emit('focus', $event)"
      />
      
      <div v-if="$slots.suffix || iconRight" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
        <slot name="suffix" />
        <button 
          v-if="iconRight === 'password'" 
          type="button"
          @click="togglePassword"
          class="focus:outline-none"
        >
          <svg v-if="showPassword" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
          </svg>
          <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.543 7-1.275 4.057-5.065 7-9.543 7-4.477 0-8.268-2.943-9.543-7z" />
          </svg>
        </button>
      </div>
    </div>
    
    <div v-if="error && showError" class="mt-1.5 flex items-center gap-1">
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
import { ref, computed, watch } from 'vue';
import { useGenerateId } from '../../composables/useGenerateId';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
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
  autocomplete: {
    type: String,
    default: ''
  },
  iconRight: {
    type: String,
    default: ''
  },
  showError: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus']);

const { generateId } = useGenerateId();
const inputId = computed(() => props.label ? generateId(props.label) : 'input');

const showPassword = ref(false);

const type = computed(() => {
  if (props.type !== 'password') return props.type;
  return showPassword.value ? 'text' : 'password';
});

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const handleBlur = (event) => {
  emit('blur', event);
};

watch(() => props.modelValue, (val) => {
  emit('update:modelValue', val);
});
</script>