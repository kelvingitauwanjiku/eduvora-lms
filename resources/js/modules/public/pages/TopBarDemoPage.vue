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
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Enhanced TopBar</h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">A modern, feature-rich navigation bar inspired by Academy-LMS but elevated with contemporary design.</p>
            </div>

            <!-- Feature Cards -->
            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <!-- Contact Info -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Contact Info</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Display phone and email with elegant icons. Clickable tel: and mailto: links for instant communication.</p>
                </div>

                <!-- Language Selector -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Language Selector</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Smooth dropdown with flag icons, native names, and localStorage persistence. Fully keyboard accessible.</p>
                </div>

                <!-- Social Links -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Social Integration</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Twitter, LinkedIn, Facebook, Instagram, YouTube icons with hover animations and configurable URLs.</p>
                </div>

                <!-- Responsive Design -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Fully Responsive</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Desktop horizontal layout, tablet condensed, mobile slide-in panel. All breakpoints optimized.</p>
                </div>

                <!-- Glassmorphism -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 text-blue-600 dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Modern Aesthetics</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Glassmorphism blur, subtle gradients, smooth animations, and refined typography.</p>
                </div>

                <!-- Dark Mode -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Dark Mode Support</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Complete dark theme integration with proper contrast ratios and smooth transitions.</p>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 shadow-xl text-white mb-12">
                <div class="grid md:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-4xl font-bold mb-2">7+</div>
                        <div class="text-blue-100">Languages Supported</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold mb-2">5+</div>
                        <div class="text-blue-100">Social Platforms</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold mb-2">100%</div>
                        <div class="text-blue-100">Responsive</div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center">
                <p class="text-gray-600 dark:text-gray-300 mb-4">Ready to use this in your Eduvora project?</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-600/30 transition-all transform hover:scale-105">
                        Get Started
                    </button>
                    <button class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl hover:border-blue-600 dark:hover:border-blue-400 transition-all">
                        View Documentation
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    // Scroll to top on page load
    window.scrollTo(0, 0);
});
</script>
