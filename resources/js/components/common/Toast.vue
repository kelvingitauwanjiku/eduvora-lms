<template>
  <Teleport to="body">
    <TransitionGroup 
      name="toast" 
      tag="div"
      class="fixed top-4 right-4 z-[100] flex flex-col gap-3 max-w-sm w-full pointer-events-none"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'pointer-events-auto rounded-xl shadow-2xl border backdrop-blur-sm',
          'flex items-start gap-3 p-4 min-w-[300px]',
          variantClasses[toast.variant]
        ]"
      >
        <div class="shrink-0 mt-0.5">
          <component :is="getIcon(toast.variant)" class="w-6 h-6" />
        </div>
        
        <div class="flex-1 min-w-0">
          <h4 v-if="toast.title" class="font-semibold text-sm">
            {{ toast.title }}
          </h4>
          <p class="text-sm opacity-90 mt-0.5">
            {{ toast.message }}
          </p>
        </div>
        
        <button 
          @click="removeToast(toast.id)"
          class="shrink-0 p-1 rounded-lg hover:bg-black/10 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </Teleport>
</template>

<script setup>
import { computed, h } from 'vue';
import { useToast } from '../../composables/useToast';

const { toasts, removeToast } = useToast();

const variantClasses = {
  success: 'bg-emerald-50/95 dark:bg-emerald-900/90 border-emerald-200 dark:border-emerald-700 text-emerald-900 dark:text-emerald-100',
  error: 'bg-red-50/95 dark:bg-red-900/90 border-red-200 dark:border-red-700 text-red-900 dark:text-red-100',
  warning: 'bg-amber-50/95 dark:bg-amber-900/90 border-amber-200 dark:border-amber-700 text-amber-900 dark:text-amber-100',
  info: 'bg-blue-50/95 dark:bg-blue-900/90 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100'
};

const getIcon = (variant) => {
  const icons = {
    success: {
      render() {
        return h('svg', { class: 'w-6 h-6 text-emerald-500', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]);
      }
    },
    error: {
      render() {
        return h('svg', { class: 'w-6 h-6 text-red-500', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]);
      }
    },
    warning: {
      render() {
        return h('svg', { class: 'w-6 h-6 text-amber-500', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })
        ]);
      }
    },
    info: {
      render() {
        return h('svg', { class: 'w-6 h-6 text-blue-500', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
          h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]);
      }
    }
  };
  return icons[variant] || icons.info;
};
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>