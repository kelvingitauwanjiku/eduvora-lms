import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { notificationApi } from '../services/api';

export const useNotificationStore = defineStore('notification', () => {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const loading = ref(false);

    async function fetchNotifications() {
        loading.value = true;
        try {
            const { data } = await notificationApi.getAll();
            notifications.value = data.data || data;
            updateUnreadCount();
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
        } finally {
            loading.value = false;
        }
    }

    async function fetchUnreadCount() {
        try {
            const { data } = await notificationApi.getUnreadCount();
            unreadCount.value = data.count || data;
        } catch (error) {
            console.error('Failed to fetch unread count:', error);
        }
    }

    async function markAsRead(id) {
        try {
            await notificationApi.markAsRead(id);
            const notification = notifications.value.find(n => n.id === id);
            if (notification) {
                notification.read = true;
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async function markAllRead() {
        try {
            await notificationApi.markAllAsRead();
            notifications.value.forEach(n => n.read = true);
            unreadCount.value = 0;
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    }

    function updateUnreadCount() {
        unreadCount.value = notifications.value.filter(n => !n.read).length;
    }

    return {
        notifications,
        unreadCount,
        loading,
        fetchNotifications,
        fetchUnreadCount,
        markAsRead,
        markAllRead
    };
});
