<template>
    <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-40 transition-all">
        <!-- Left: Logo & Menu -->
        <div class="flex items-center gap-4">
            <button @click="$emit('toggle-sidebar')" class="lg:hidden p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <router-link to="/" class="flex items-center gap-2">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                    <span class="text-white font-bold text-lg">E</span>
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white hidden md:block">Eduvora</span>
            </router-link>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">
            <!-- Search -->
            <button class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all hidden md:flex">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <!-- Cart -->
            <router-link to="/student/cart" class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.427 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span v-if="cartCount > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">{{ cartCount }}</span>
            </router-link>

            <!-- Notifications -->
            <NotificationDropdown />

            <!-- Profile Dropdown -->
            <ProfileDropdown />
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { useCartStore } from '../../stores/cart';
import NotificationDropdown from './NotificationDropdown.vue';
import ProfileDropdown from './ProfileDropdown.vue';

defineEmits(['toggle-sidebar']);

const cartStore = useCartStore();
const cartCount = computed(() => cartStore.items.length);
</script>
