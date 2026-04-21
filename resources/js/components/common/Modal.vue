<template>
  <Teleport to="body">
    <Transition name="modal">
      <div 
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="handleBackdropClick"
      >
        <div class="min-h-screen px-4 flex items-center justify-center">
          <Transition name="overlay">
            <div 
              v-if="show" 
              class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
              @click="handleBackdropClick"
            ></div>
          </Transition>
          
          <Transition name="content">
            <div 
              v-if="show"
              :class="[
                'relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl transition-all',
                sizeClasses,
                customClass
              ]"
              :style="{ maxHeight: maxHeight }"
            >
              <div v-if="$slots.header || title || closeable" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                  <slot name="header">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                      {{ title }}
                    </h3>
                  </slot>
                  
                  <button 
                    v-if="closeable" 
                    @click="close"
                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
              
              <div 
                class="px-6 py-4 overflow-y-auto"
                :style="{ maxHeight: contentMaxHeight }"
              >
                <slot />
              </div>
              
              <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-2xl">
                <slot name="footer" />
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(value)
  },
  closeable: {
    type: Boolean,
    default: true
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  },
  closeOnEscape: {
    type: Boolean,
    default: true
  },
  maxHeight: {
    type: String,
    default: '90vh'
  },
  customClass: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['close', 'update:show']);

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'w-full max-w-xs',
    sm: 'w-full max-w-sm',
    md: 'w-full max-w-lg',
    lg: 'w-full max-w-2xl',
    xl: 'w-full max-w-4xl',
    '2xl': 'w-full max-w-6xl',
    full: 'w-full max-w-full'
  };
  return sizes[props.size];
});

const contentMaxHeight = computed(() => {
  if (props.$slots.header || props.title || props.closeable) {
    return 'calc(90vh - 8rem)';
  }
  return 'calc(90vh - 4rem)';
});

const close = () => {
  emit('update:show', false);
  emit('close');
};

const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    close();
  }
};

watch(() => props.show, (value) => {
  if (value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});

const handleEscape = (e) => {
  if (e.key === 'Escape' && props.show && props.closeOnEscape) {
    close();
  }
};

if (typeof window !== 'undefined') {
  window.addEventListener('keydown', handleEscape);
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

.content-enter-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.content-leave-active {
  transition: all 0.2s ease;
}

.content-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}

.content-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>