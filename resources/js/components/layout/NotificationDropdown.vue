<template>
    <div class="relative">
        <!-- Notification Button -->
        <button @click="toggleOpen"
                class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200 relative group">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span v-if="unreadCount > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
            <span class="absolute inset-0 rounded-xl bg-blue-500/20 scale-0 group-hover:scale-100 transition-transform duration-200"></span>
        </button>

        <!-- Dropdown -->
        <transition name="dropdown">
            <div v-if="isOpen" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                <!-- Header -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                        <p v-if="unreadCount > 0" class="text-xs text-gray-500 dark:text-gray-400">{{ unreadCount }} unread</p>
                    </div>
                    <button v-if="unreadCount > 0" @click="markAllRead" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 font-medium">
                        Mark all read
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-80 overflow-y-auto">
                    <div v-for="notification in notifications" :key="notification.id"
                         class="flex items-start gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer border-b border-gray-50 dark:border-gray-700/50 last:border-0"
                         :class="!notification.read ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''"
                         @click="handleClick(notification)">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="notification.bgColor">
                            <component :is="notification.icon" class="w-5 h-5" :class="notification.iconColor" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ notification.title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ notification.message }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ notification.time }}</p>
                        </div>
                        <div v-if="!notification.read" class="w-2 h-2 bg-blue-500 rounded-full mt-2 shrink-0"></div>
                    </div>

                    <div v-if="notifications.length === 0" class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-3 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-2">
                    <router-link to="/notifications" class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 py-2 rounded-lg transition">
                        View all
                    </router-link>
                    <button @click="markAllRead" v-if="unreadCount > 0" class="text-center text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 py-2 rounded-lg transition">
                        Mark all read
                    </button>
                </div>
            </div>
        </transition>

        <!-- Backdrop -->
        <div v-if="isOpen" class="fixed inset-0 z-40" @click="close"></div>
    </div>
</template>

<script setup>
import { ref, computed, h, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useNotificationStore } from '../../stores/notification';

const emit = defineEmits(['update:modelValue']);

const authStore = useAuthStore();
const notificationStore = useNotificationStore();

const isOpen = ref(false);

const notifications = computed(() => notificationStore.notifications);
const unreadCount = computed(() => notificationStore.unreadCount);

function toggleOpen() {
    isOpen.value = !isOpen.value;
}

function close() {
    isOpen.value = false;
}

function handleClick(notification) {
    notificationStore.markAsRead(notification.id);
    // Navigate to related page if needed
    close();
}

function markAllRead() {
    notificationStore.markAllRead();
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
