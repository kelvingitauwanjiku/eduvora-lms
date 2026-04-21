<template>
    <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-40 transition-all">
        <!-- Left: Menu Toggle -->
        <div class="flex items-center gap-4">
            <button @click="$emit('toggle-sidebar')" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo on mobile -->
            <router-link to="/instructor" class="lg:hidden flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">E</span>
                </div>
                <span class="text-lg font-bold text-gray-900 dark:text-white">Instructor</span>
            </router-link>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">
            <!-- Notifications -->
            <NotificationDropdown />

            <!-- Messages -->
            <button class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span v-if="unreadMessages > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ unreadMessages }}</span>
            </button>

            <!-- Divider -->
            <div class="w-px h-6 bg-gray-200 dark:bg-gray-700 mx-1"></div>

            <!-- Profile Dropdown -->
            <ProfileDropdown />
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import NotificationDropdown from './NotificationDropdown.vue';
import ProfileDropdown from './ProfileDropdown.vue';

defineEmits(['toggle-sidebar']);

const authStore = useAuthStore();
const unreadMessages = ref(3);
</script>


