<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p class="mt-1 text-gray-500">{{ unreadCount > 0 ? `${unreadCount} unread` : 'All caught up' }}</p>
            </div>
            <button v-if="notifications.length > 0" @click="markAllRead" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                Mark all as read
            </button>
        </div>

        <div class="flex gap-2 mb-6">
            <button v-for="type in notificationTypes" :key="type.value" @click="filter = type.value"
                :class="filter === type.value ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 hover:border-gray-300 transition">
                {{ type.label }}
            </button>
        </div>

        <div v-if="loading" class="space-y-2">
            <div v-for="i in 5" :key="i" class="animate-pulse bg-white rounded-xl border p-4">
                <div class="h-10 bg-gray-200 rounded"></div>
            </div>
        </div>

        <div v-else-if="filteredNotifications.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5a1 1 0 001.707-1.707l-2.828 2.828A1 1 0 0115 17.586V13a1 1 0 00-1-1h-4a1 1 0 00-1 1v4.586l-2.829-2.828A1 1 0 014 13h5v4h6v-4z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No notifications</h3>
            <p class="text-gray-500 mt-1">{{ filter === 'all' ? 'You\'re all caught up!' : 'No notifications in this category' }}</p>
        </div>

        <div v-else class="space-y-2">
            <div v-for="notif in filteredNotifications" :key="notif.id" 
                class="bg-white rounded-xl border p-4 flex items-start gap-4 cursor-pointer hover:bg-gray-50 transition"
                :class="!notif.read ? 'border-l-4 border-l-blue-500' : ''" @click="handleClick(notif)">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" :class="getNotificationStyle(notif).bg">
                    <svg class="w-5 h-5" :class="getNotificationStyle(notif).color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="notif.type === 'enrollment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        <path v-else-if="notif.type === 'certificate'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.241-.247 3.47 3.47 0 014.438 0 3.42 3.42 0 001.241.247 3.42 3.42 0 012.814.013 3.47 3.47 0 012.814.013 3.42 3.42 0 001.241.247c.85.215 1.525.85 1.741 1.764 1.166.49 1.914 1.597 1.914 2.855 0 .912-.485 1.743-1.278 2.197a3.42 3.42 0 00-.506 1.837" />
                        <path v-else-if="notif.type === 'message'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        <path v-else-if="notif.type === 'payment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5a1 1 0 001.707-1.707l-2.828 2.828A1 1 0 0115 17.586V13a1 1 0 00-1-1h-4a1 1 0 00-1 1v4.586l-2.829-2.828A1 1 0 014 13h5v4h6v-4z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-gray-900" :class="notif.read ? '' : 'font-medium'">{{ notif.message }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ formatTime(notif.created_at) }}</p>
                </div>
                <button v-if="!notif.read" @click.stop="markAsRead(notif)" class="text-gray-400 hover:text-gray-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { notificationApi } from '@/services/api';

const loading = ref(true);
const notifications = ref([]);
const filter = ref('all');

const notificationTypes = [
    { label: 'All', value: 'all' },
    { label: 'Courses', value: 'enrollment' },
    { label: 'Certificates', value: 'certificate' },
    { label: 'Messages', value: 'message' },
    { label: 'Payments', value: 'payment' },
    { label: 'System', value: 'system' },
];

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const filteredNotifications = computed(() => {
    if (filter.value === 'all') return notifications.value;
    return notifications.value.filter(n => n.type === filter.value);
});

function getNotificationStyle(notif) {
    const styles = {
        enrollment: { bg: 'bg-blue-100', color: 'text-blue-600' },
        certificate: { bg: 'bg-green-100', color: 'text-green-600' },
        message: { bg: 'bg-purple-100', color: 'text-purple-600' },
        payment: { bg: 'bg-yellow-100', color: 'text-yellow-600' },
        system: { bg: 'bg-gray-100', color: 'text-gray-600' },
    };
    return styles[notif.type] || styles.system;
}

function formatTime(date) {
    if (!date) return '';
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return `${Math.floor(diff / 60000)} min ago`;
    if (diff < 86400000) return `${Math.floor(diff / 3600000)} hours ago`;
    if (diff < 604800000) return `${Math.floor(diff / 86400000)} days ago`;
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

async function fetchNotifications() {
    try {
        loading.value = true;
        const { data } = await notificationApi.getAll();
        notifications.value = data.data || [];
    } catch (error) {
        console.error('Error fetching notifications:', error);
    } finally {
        loading.value = false;
    }
}

async function markAsRead(notif) {
    try {
        await notificationApi.markAsRead(notif.id);
        notif.read = true;
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

async function markAllRead() {
    try {
        await notificationApi.markAllAsRead();
        notifications.value.forEach(n => n.read = true);
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
}

function handleClick(notif) {
    if (!notif.read) {
        markAsRead(notif);
    }
    if (notif.action_url) {
        window.location.href = notif.action_url;
    }
}

onMounted(() => {
    fetchNotifications();
});
</script>