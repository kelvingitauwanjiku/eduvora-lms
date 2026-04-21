import { reactive, readonly, computed } from 'vue';

let toastId = 0;

const state = reactive({
  toasts: []
});

export function useToast() {
  const toasts = computed(() => state.toasts);

  const addToast = ({ 
    title = '', 
    message, 
    variant = 'info', 
    duration = 5000,
    dismissible = true 
  }) => {
    const id = ++toastId;
    
    state.toasts.push({
      id,
      title,
      message,
      variant,
      duration,
      dismissible
    });

    if (duration > 0) {
      setTimeout(() => {
        removeToast(id);
      }, duration);
    }

    return id;
  };

  const removeToast = (id) => {
    const index = state.toasts.findIndex(t => t.id === id);
    if (index !== -1) {
      state.toasts.splice(index, 1);
    }
  };

  const success = (message, options = {}) => {
    return addToast({ message, variant: 'success', ...options });
  };

  const error = (message, options = {}) => {
    return addToast({ message, variant: 'error', ...options });
  };

  const warning = (message, options = {}) => {
    return addToast({ message, variant: 'warning', ...options });
  };

  const info = (message, options = {}) => {
    return addToast({ message, variant: 'info', ...options });
  };

  return {
    toasts,
    addToast,
    removeToast,
    success,
    error,
    warning,
    info
  };
}

export { state as toastState };
