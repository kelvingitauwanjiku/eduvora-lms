<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 shadow-lg shadow-blue-600/30 mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Enhanced App Header</h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">Next-generation navigation with mega menus, advanced search, and rich user experience.</p>
            </div>

            <!-- Feature Grid -->
            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <!-- Mega Menu -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Mega Menu</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Two-column grid with category icons, course counts, and flyout child menus. Hover to explore.</p>
                </div>

                <!-- Advanced Search -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Smart Search</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Desktop inline with ⌘K hint. Mobile triggers full-screen overlay with large input.</p>
                </div>

                <!-- User Profile Dropdown -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Profile Dropdown</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Large card with avatar, role badge, online status, and quick-actions with badge counts.</p>
                </div>

                <!-- Mobile Offcanvas -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Mobile Menu</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Full-screen offcanvas with user panel, all navigation, and social footer. Smooth slide-in animation.</p>
                </div>

                <!-- Badge Counts -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Live Counters</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Real-time badge counts for cart, wishlist, notifications, and messages. Pulse animation on updates.</p>
                </div>

                <!-- Dark Mode -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Dark Mode</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Complete dark theme with proper contrast ratios and smooth color transitions throughout.</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 shadow-xl text-white mb-12">
                <div class="grid md:grid-cols-4 gap-6 text-center">
                    <div>
                        <div class="text-4xl font-bold mb-2">8+</div>
                        <div class="text-blue-100">Menu Items</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold mb-2">20+</div>
                        <div class="text-blue-100">Categories</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold mb-2">3</div>
                        <div class="text-blue-100">Counters</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold mb-2">100%</div>
                        <div class="text-blue-100">Responsive</div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center">
                <p class="text-gray-600 dark:text-gray-300 mb-4">Ready to upgrade your Eduvora navigation?</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-600/30 transition-all transform hover:scale-105">
                        View Demo
                    </button>
                    <button class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:border-blue-600 dark:hover:border-blue-400 transition-all">
                        Documentation
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import EnhancedAppHeader from '../components/layout/EnhancedAppHeader.vue';

onMounted(() => {
    window.scrollTo(0, 0);
});
</script>
