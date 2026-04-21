<template>
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 min-h-screen sticky top-0 hidden lg:block">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700">
                <router-link to="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">E</span>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Eduvora</span>
                </router-link>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1">
                <router-link v-for="item in navItems" :key="item.path" :to="item.path"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                    :class="$route.path === item.path ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-l-4 border-blue-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <span class="text-sm font-medium">{{ item.name }}</span>
                </router-link>
            </nav>

            <!-- Quick Add -->
            <div class="p-4 mt-6 border-t border-gray-200 dark:border-gray-700">
                <router-link to="/student/create-bundle" 
                    class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-semibold">Browse Courses</span>
                </router-link>
            </div>
        </aside>

        <!-- Mobile Header -->
        <div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center px-4 z-30">
            <button @click="mobileMenuOpen = true" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="flex-1 flex items-center justify-center">
                <span class="text-lg font-bold text-gray-900 dark:text-white">Eduvora</span>
            </div>
            <router-link to="/student/cart" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl relative">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.427 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span v-if="cartCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">{{ cartCount }}</span>
            </router-link>
        </div>

        <!-- Mobile Menu Overlay -->
        <div v-if="mobileMenuOpen" class="lg:hidden fixed inset-0 bg-black/50 z-40" @click="mobileMenuOpen = false">
            <div class="w-64 bg-white dark:bg-gray-900 h-full p-4" @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Menu</span>
                    <button @click="mobileMenuOpen = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="space-y-1">
                    <router-link v-for="item in navItems" :key="item.path" :to="item.path"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all"
                        :class="$route.path === item.path ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <span class="text-sm font-medium">{{ item.name }}</span>
                    </router-link>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-0">
            <div class="pt-16 lg:pt-0">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
const cartCount = ref(2);

const navItems = [
    { name: 'Dashboard', path: '/student', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'My Courses', path: '/student/courses', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
    { name: 'Live Classes', path: '/student/live-classes', icon: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' },
    { name: 'Wishlist', path: '/student/wishlist', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
    { name: 'Cart', path: '/student/cart', icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.427 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Purchases', path: '/student/purchases', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Certificates', path: '/student/certificates', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.241-.247 3.47 3.47 0 014.438 0 3.42 3.42 0 001.241.247 3.42 3.42 0 012.814.013 3.47 3.47 0 012.814.013 3.42 3.42 0 001.241.247c.85.215 1.525.85 1.741 1.764 1.194.549 1.944 1.585 1.944 2.785 0 .715-.373 1.363-1.007 1.73a3.42 3.42 0 00-.506 1.837' },
    { name: 'Messages', path: '/student/messages', icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' },
    { name: 'Notifications', path: '/student/notifications', icon: 'M15 17h5a1 1 0 001.707-1.707l-2.828 2.828A1 1 0 0115 17.586V13a1 1 0 00-1-1h-4a1 1 0 00-1 1v4.586l-2.829-2.828A1 1 0 014 13h5v4h6v-4z' },
    { name: 'Support', path: '/student/support', icon: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'Profile', path: '/student/profile', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
];
</script>
