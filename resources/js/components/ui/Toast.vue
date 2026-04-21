<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-50 space-y-2">
            <TransitionGroup name="toast">
                <div v-for="toast in toasts" :key="toast.id" 
                    :class="['px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 max-w-sm', toastClasses[toast.type]]">
                    <svg v-if="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else-if="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-medium">{{ toast.message }}</p>
                    <button @click="removeToast(toast.id)" class="ml-auto text-current opacity-70 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive } from 'vue';

const toasts = ref([]);
let nextId = 0;

const toastClasses = {
    success: 'bg-green-50 text-green-800 border border-green-200',
    error: 'bg-red-50 text-red-800 border border-red-200',
    warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
    info: 'bg-blue-50 text-blue-800 border border-blue-200',
};

function addToast(message, type = 'info', duration = 5000) {
    const id = nextId++;
    toasts.value.push({ id, message, type });
    if (duration > 0) {
        setTimeout(() => removeToast(id), duration);
    }
}

function removeToast(id) {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index > -1) toasts.value.splice(index, 1);
}

defineExpose({ success: (msg) => addToast(msg, 'success'), error: (msg) => addToast(msg, 'error'), warning: (msg) => addToast(msg, 'warning'), info: (msg) => addToast(msg, 'info') });
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateX(100%); }
</style>