<template>
    <div class="relative">
        <!-- Profile Button -->
        <button @click="toggleOpen"
                class="flex items-center gap-3 p-1.5 pr-4 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200 group">
            <img :src="authStore.user?.avatar || '/images/avatar.png'"
                 class="w-9 h-9 rounded-xl object-cover border-2 border-gray-200 dark:border-gray-700 group-hover:border-blue-500 transition-colors"
                 alt="Profile">
            <div class="hidden lg:block text-left">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[120px]">
                    {{ authStore.user?.name || 'Admin' }}
                </p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 capitalize">{{ userRole }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown -->
        <transition name="dropdown">
            <div v-if="isOpen" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                <!-- User Info -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="font-semibold text-gray-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ authStore.user?.email }}</p>
                </div>

                <!-- Menu Items -->
                <div class="p-2 space-y-1">
                    <router-link :to="profileRoute"
                                 class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Profile
                    </router-link>
                    <router-link :to="settingsRoute"
                                 class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </router-link>
                    <router-link to="/" target="_blank"
                                 class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View Website
                    </router-link>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                <!-- Logout -->
                <div class="p-2">
                    <button @click="handleLogout"
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign Out
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const isOpen = ref(false);

const toggleOpen = () => {
    isOpen.value = !isOpen.value;
};

const userRole = computed(() => {
    if (authStore.isAdmin) return 'Administrator';
    if (authStore.isInstructor) return 'Instructor';
    return 'Student';
});

const profileRoute = computed(() => {
    if (authStore.isAdmin) return '/admin/profile';
    if (authStore.isInstructor) return '/instructor/profile';
    return '/student/profile';
});

const settingsRoute = computed(() => {
    if (authStore.isAdmin) return '/admin/settings';
    return '/profile#settings';
});

async function handleLogout() {
    await authStore.logout();
    isOpen.value = false;
    router.push('/login');
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
