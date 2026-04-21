<template>
    <!-- Enhanced AppHeader - Supercharged Navigation -->
    <header
        class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200/50 dark:border-gray-700/50 shadow-sm transition-all duration-300"
        :class="{ 'h-20': isScrolled, 'h-16': !isScrolled }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-full">
                <!-- Logo Section -->
                <div class="flex items-center gap-8">
                    <router-link to="/" class="flex items-center gap-2 group">
                        <div class="relative w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/25 group-hover:shadow-blue-600/40 transition-all duration-300 transform group-hover:scale-105">
                            <span class="text-white font-bold text-xl">E</span>
                            <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white hidden sm:block">Eduvora</span>
                    </router-link>

                    <!-- Main Navigation with Mega Menu -->
                    <nav class="hidden lg:flex items-center gap-1">
                        <div v-for="item in navItems" :key="item.name" class="relative" @mouseenter="activeMegaMenu = item.name" @mouseleave="activeMegaMenu = null">
                            <!-- Regular Link -->
                            <router-link
                                v-if="!item.children"
                                :to="item.path"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-1.5"
                                :class="$route.path === item.path || $route.path.startsWith(item.path + '/')
                                    ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/30'
                                    : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            >
                                <!-- Home Icon -->
                                <svg v-if="item.name === 'Home'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <!-- Courses Icon -->
                                <svg v-else-if="item.name === 'Courses'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <!-- Bootcamp Icon -->
                                <svg v-else-if="item.name === 'Bootcamp'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                <!-- Bundles Icon -->
                                <svg v-else-if="item.name === 'Bundles'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <!-- More Icon -->
                                <svg v-else-if="item.name === 'More'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                </svg>
                                {{ item.name }}
                            </router-link>

                            <!-- Mega Menu Trigger -->
                            <button
                                v-else
                                @click="toggleMegaMenu(item.name)"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-1.5"
                                :class="activeMegaMenu === item.name
                                    ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/30'
                                    : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            >
                                <component :is="item.icon" v-if="item.icon" class="w-4 h-4" />
                                {{ item.name }}
                                <svg class="w-4 h-4 transition-transform duration-200" :class="activeMegaMenu === item.name ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Mega Menu Dropdown -->
                            <div
                                v-if="item.children && activeMegaMenu === item.name"
                                class="absolute left-1/2 -translate-x-1/2 top-full mt-3 w-[700px] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
                                @mouseleave="activeMegaMenu = null"
                            >
                                <!-- Category Icons Header -->
                                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50/50 to-transparent dark:from-gray-700/50">
                                    <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        {{ item.name === 'More' ? 'Quick Links' : 'Browse by Category' }}
                                    </div>
                                </div>

                                <div class="p-6">
                                    <!-- For Courses: Two-column grid with category cards -->
                                    <div v-if="item.name === 'Courses'" class="grid grid-cols-2 gap-x-8 gap-y-4">
                                        <div v-for="category in item.children" :key="category.slug" class="group relative">
                                            <!-- Parent Category Link -->
                                            <router-link
                                                :to="`/courses?category=${category.slug}`"
                                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 -m-3"
                                                @click="activeMegaMenu = null"
                                            >
                                                <!-- Category Icon -->
                                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/40 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-200">
                                                    <img v-if="category.icon_url" :src="category.icon_url" :alt="category.name" class="w-5 h-5 object-contain" />
                                                    <svg v-else class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                                <!-- Category Info -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                                        {{ category.name }}
                                                    </h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ category.courses_count || 0 }} courses
                                                    </p>
                                                </div>
                                                <!-- Arrow if has children -->
                                                <svg v-if="category.children && category.children.length > 0" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </router-link>

                                            <!-- Child Categories Flyout -->
                                            <div
                                                v-if="category.children && category.children.length > 0"
                                                class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 p-3 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200"
                                                @mouseenter="activeMegaMenu = item.name"
                                                @mouseleave="activeMegaMenu = null"
                                            >
                                                <div class="space-y-1">
                                                    <router-link
                                                        v-for="child in category.children"
                                                        :key="child.id"
                                                        :to="`/courses?category=${child.slug}`"
                                                        class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                                        @click="activeMegaMenu = null"
                                                    >
                                                        {{ child.name }}
                                                        <span class="float-right text-xs text-gray-400">{{ child.courses_count || 0 }}</span>
                                                    </router-link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- For More: Simple list with icons -->
                                    <div v-else class="space-y-1">
                                        <router-link
                                            v-for="sub in item.children"
                                            :key="sub.name"
                                            :to="sub.path || `/${sub.slug}`"
                                            class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all group"
                                            @click="activeMegaMenu = null"
                                        >
                                            <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <!-- Ebooks icon -->
                                                <svg v-if="sub.name === 'Ebooks'" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                                <!-- Tutor icon -->
                                                <svg v-else-if="sub.name === 'Find a Tutor'" class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <!-- Default -->
                                                <svg v-else class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <span class="font-medium">{{ sub.name }}</span>
                                                <span v-if="sub.courses_count" class="ml-2 text-xs text-gray-400">{{ sub.courses_count }} courses</span>
                                            </div>
                                        </router-link>
                                    </div>
                                </div>

                                <!-- Footer with "View All Courses" (only for Courses menu) -->
                                <div v-if="item.name === 'Courses'" class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                                    <router-link
                                        to="/courses"
                                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-xl transition-all duration-200"
                                        @click="activeMegaMenu = null"
                                    >
                                        View All Courses
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </router-link>
                                </div>
                            </div>
                                </div>

                                <div class="p-6">
                                    <!-- Two-column layout for categories -->
                                    <div class="grid grid-cols-2 gap-x-8 gap-y-4">
                                        <div v-for="category in item.children" :key="category.slug" class="group relative">
                                            <!-- Parent Category Link -->
                                            <router-link
                                                :to="`/courses?category=${category.slug}`"
                                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 -m-3"
                                                @click="activeMegaMenu = null"
                                            >
                                                <!-- Category Icon -->
                                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/40 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-200">
                                                    <img v-if="category.icon_url" :src="category.icon_url" :alt="category.name" class="w-5 h-5 object-contain" />
                                                    <svg v-else class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>

                                                <!-- Category Info -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                                                        {{ category.name }}
                                                    </h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ category.courses_count || 0 }} courses
                                                    </p>
                                                </div>

                                                <!-- Arrow if has children -->
                                                <svg v-if="category.children && category.children.length > 0" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </router-link>

                                            <!-- Child Categories Flyout -->
                                            <div
                                                v-if="category.children && category.children.length > 0"
                                                class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 p-3 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200"
                                                @mouseenter="activeMegaMenu = item.name"
                                                @mouseleave="activeMegaMenu = null"
                                            >
                                                <div class="space-y-1">
                                                    <router-link
                                                        v-for="child in category.children"
                                                        :key="child.id"
                                                        :to="`/courses?category=${child.slug}`"
                                                        class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                                        @click="activeMegaMenu = null"
                                                    >
                                                        {{ child.name }}
                                                        <span class="float-right text-xs text-gray-400">{{ child.courses_count || 0 }}</span>
                                                    </router-link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                    <!-- "More" Dropdown Items with Icons -->
                                    <div v-if="item.name === 'More'" class="space-y-1">
                                        <!-- Ebooks -->
                                        <router-link
                                            to="/ebooks"
                                            class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all group"
                                            @click="activeMegaMenu = null"
                                        >
                                            <div class="p-1.5 rounded-md bg-indigo-100 dark:bg-indigo-900/30 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-900/50 transition-colors">
                                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Ebooks</span>
                                        </router-link>

                                        <!-- Find a Tutor -->
                                        <router-link
                                            to="/tutors"
                                            class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all group"
                                            @click="activeMegaMenu = null"
                                        >
                                            <div class="p-1.5 rounded-md bg-pink-100 dark:bg-pink-900/30 group-hover:bg-pink-200 dark:group-hover:bg-pink-900/50 transition-colors">
                                                <svg class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Find a Tutor</span>
                                        </router-link>

                                        <!-- Course Bundles -->
                                        <router-link
                                            to="/course-bundles"
                                            class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all group"
                                            @click="activeMegaMenu = null"
                                        >
                                            <div class="p-1.5 rounded-md bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Course Bundles</span>
                                        </router-link>
                                    </div>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- Right: Search, Actions, User -->
                <div class="flex items-center gap-2 lg:gap-4">
                    <!-- Desktop Search -->
                    <div class="relative hidden md:block group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text"
                            placeholder="Search courses..."
                            class="block w-64 xl:w-96 pl-10 pr-4 py-2.5 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-800/50 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white dark:focus:bg-gray-800 transition-all duration-200"
                        />
                        <kbd class="absolute right-3 top-1/2 -translate-y-1/2 hidden lg:inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-gray-400 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">
                            <span class="text-xs">⌘</span>K
                        </kbd>
                    </div>

                    <!-- Mobile Search Toggle -->
                    <button
                        @click="showMobileSearch = true"
                        class="md:hidden p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        aria-label="Search"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <!-- Cart -->
                    <router-link
                        to="/cart"
                        class="relative p-2.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-xl transition-all duration-200 group"
                        title="Shopping Cart"
                    >
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span
                            v-if="cartCount > 0"
                            class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 bg-gradient-to-br from-red-500 to-pink-500 text-white text-xs font-bold rounded-full animate-pulse-once"
                        >
                            {{ cartCount > 99 ? '99+' : cartCount }}
                        </span>
                    </router-link>

                    <!-- Wishlist -->
                    <router-link
                        v-if="authStore.isAuthenticated"
                        to="/wishlist"
                        class="relative p-2.5 text-gray-500 dark:text-gray-400 hover:text-pink-600 dark:hover:text-pink-400 hover:bg-pink-50 dark:hover:bg-pink-900/30 rounded-xl transition-all duration-200 group"
                        title="Wishlist"
                    >
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span
                            v-if="wishlistCount > 0"
                            class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 bg-gradient-to-br from-pink-500 to-rose-500 text-white text-xs font-bold rounded-full animate-pulse-once"
                        >
                            {{ wishlistCount > 99 ? '99+' : wishlistCount }}
                        </span>
                    </router-link>

                    <!-- Hamburger Menu (Mobile) -->
                    <button
                        @click="showMobileMenu = true"
                        class="lg:hidden p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        aria-label="Open menu"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Notifications (Authenticated) -->
                    <template v-if="authStore.isAuthenticated">
                        <router-link
                            to="/notifications"
                            class="relative p-2.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-xl transition-all duration-200 group"
                            title="Notifications"
                        >
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span
                                v-if="unreadNotificationsCount > 0"
                                class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full animate-bounce"
                            >
                                {{ unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount }}
                            </span>
                        </router-link>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 p-1.5 pr-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 group"
                                aria-expanded="true"
                                aria-haspopup="true"
                            >
                                <div class="relative">
                                    <img
                                        :src="authStore.user?.avatar || '/images/avatar-default.png'"
                                        alt="User avatar"
                                        class="w-9 h-9 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 group-hover:border-blue-500 dark:group-hover:border-blue-400 transition-colors"
                                    />
                                    <!-- Online indicator -->
                                    <span
                                        v-if="authStore.user?.is_online"
                                        class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"
                                        title="Online"
                                    ></span>
                                </div>
                                <span class="hidden lg:block text-sm font-medium text-gray-700 dark:text-gray-200 max-w-[100px] truncate">
                                    {{ authStore.user?.name?.split(' ')[0] }}
                                </span>
                                <svg
                                    class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                    :class="userMenuOpen ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <transition
                                enter-active-class="transition ease-out duration-150"
                                enter-from-class="transform -translate-y-2 opacity-0 scale-95"
                                enter-to-class="transform translate-y-0 opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="transform translate-y-0 opacity-100 scale-100"
                                leave-to-class="transform -translate-y-2 opacity-0 scale-95"
                            >
                                <div
                                    v-if="userMenuOpen"
                                    class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 py-2 z-50 overflow-hidden"
                                    @click.stop
                                >
                                    <!-- User Info Header -->
                                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-700/50">
                                        <div class="flex items-center gap-3">
                                            <img
                                                :src="authStore.user?.avatar || '/images/avatar-default.png'"
                                                alt="Avatar"
                                                class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-gray-600 shadow-sm"
                                            />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                    {{ authStore.user?.name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ authStore.user?.email }}
                                                </p>
                                                <div class="flex items-center gap-1 mt-1">
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full"
                                                        :class="roleBadgeClass"
                                                    >
                                                        {{ authStore.user?.role }}
                                                    </span>
                                                    <span
                                                        v-if="authStore.user?.is_online"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                                    >
                                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                        Online
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="py-2">
                                        <!-- Dashboard -->
                                        <router-link
                                            :to="dashboardLink"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Dashboard</span>
                                        </router-link>

                                        <!-- Profile -->
                                        <router-link
                                            :to="profileLink"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">My Profile</span>
                                        </router-link>

                                        <!-- My Courses -->
                                        <router-link
                                            to="/student/courses"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">My Courses</span>
                                            <span v-if="enrolledCount > 0" class="ml-auto bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 text-xs font-bold px-2 py-0.5 rounded-full">
                                                {{ enrolledCount }}
                                            </span>
                                        </router-link>

                                        <!-- Wishlist -->
                                        <router-link
                                            to="/wishlist"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Wishlist</span>
                                        </router-link>

                                        <!-- Messages -->
                                        <router-link
                                            to="/messages"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Messages</span>
                                            <span v-if="unreadMessagesCount > 0" class="ml-auto bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-xs font-bold px-2 py-0.5 rounded-full">
                                                {{ unreadMessagesCount }}
                                            </span>
                                        </router-link>

                                        <!-- My Bootcamps -->
                                        <router-link
                                            to="/student/bootcamps"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-.658.953l-4.5 2.276a1 1 0 01-.894 0l-4.5-2.276A1 1 0 0110 15.382V8.618a1 1 0 01.658-.953l4.5-2.276z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l8.568 4.714a1 1 0 01.864 0L21 3" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">My Bootcamps</span>
                                        </router-link>

                                        <!-- My Ebooks -->
                                        <router-link
                                            to="/student/ebooks"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">My Ebooks</span>
                                        </router-link>

                                        <!-- Purchase History -->
                                        <router-link
                                            to="/student/purchase-history"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Purchase History</span>
                                        </router-link>

                                        <!-- Customer Support -->
                                        <router-link
                                            to="/student/support"
                                            class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 12.708m0 5.656l3.536-3.536M12.708 5.636L9.172 9.172m5.656 0l-3.536 3.536M4 4l4 4M21 4l-4 4M4 20l4-4M21 20l-4-4" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 8.5l7 7M3 3l18 18" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Customer Support</span>
                                        </router-link>

                                        <!-- Divider -->
                                        <hr class="my-2 border-gray-100 dark:border-gray-700">

                                        <!-- Logout -->
                                        <button
                                            @click="handleLogout"
                                            class="w-full flex items-center gap-3 px-5 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors group"
                                        >
                                            <div class="p-1.5 rounded-lg bg-red-100 dark:bg-red-900/30 group-hover:bg-red-200 dark:group-hover:bg-red-900/50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                            </div>
                                            <span class="font-medium">Logout</span>
                        </button>
                    </div>
                </div>
        </div>
    </header>

    <!-- Floating Mobile Search Panel -->
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-if="showMobileSearch"
            class="fixed inset-0 z-50 md:hidden"
        >
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                @click="showMobileSearch = false"
            ></div>
            <!-- Search Panel -->
            <div class="absolute inset-x-0 top-0 bg-white dark:bg-gray-900 shadow-2xl border-b border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center gap-3">
                    <button
                        @click="showMobileSearch = false"
                        class="p-2 -ml-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <input
                        ref="mobileSearchInput"
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        type="text"
                        placeholder="Search courses..."
                        class="flex-1 px-4 py-3 text-base border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        autofocus
                    />
                    <button
                        @click="handleSearch"
                        class="p-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 text-center">
                    Press Enter to search or Esc to close
                </p>
            </div>
        </div>
    </transition>

    <!-- Mobile Offcanvas Menu -->
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 -translate-x-full"
        enter-to-class="opacity-100 translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0"
        leave-to-class="opacity-0 -translate-x-full"
    >
        <div
            v-if="showMobileMenu"
            class="fixed inset-y-0 left-0 z-[60] w-80 max-w-[85vw] bg-white dark:bg-gray-900 shadow-2xl border-r border-gray-200 dark:border-gray-700 flex flex-col"
            @click.self="showMobileMenu = false"
        >
            <!-- Mobile Menu Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                <router-link to="/" class="flex items-center gap-2" @click="showMobileMenu = false">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold">E</span>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Eduvora</span>
                </router-link>
                <button
                    @click="showMobileMenu = false"
                    class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Content -->
            <div class="flex-1 overflow-y-auto p-4">
                <!-- User Info (if authenticated) -->
                <div v-if="authStore.isAuthenticated" class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800">
                    <div class="flex items-center gap-3">
                        <img
                            :src="authStore.user?.avatar || '/images/avatar-default.png'"
                            alt="Avatar"
                            class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-gray-600 shadow"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ authStore.user?.email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                {{ authStore.user?.role }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-1">
                    <router-link
                        v-for="item in navItems"
                        :key="item.name"
                        :to="item.path"
                        class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl transition-all"
                        :class="$route.path === item.path
                            ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'"
                        @click="showMobileMenu = false"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Home -->
                            <svg v-if="item.name === 'Home'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <!-- Courses -->
                            <svg v-else-if="item.name === 'Courses'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <!-- Bootcamp -->
                            <svg v-else-if="item.name === 'Bootcamp'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            <!-- Bundles -->
                            <svg v-else-if="item.name === 'Bundles'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <!-- More -->
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                            {{ item.name }}
                        </div>
                        <svg v-if="item.children" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </router-link>

                    <!-- More: expandable submenu -->
                    <div v-for="item in navItems.filter(i => i.name === 'More')" :key="'more-sub'">
                        <div v-for="sub in item.children" :key="sub.name" class="pl-12">
                            <router-link
                                :to="sub.path"
                                class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all"
                                @click="showMobileMenu = false"
                            >
                                <span>{{ sub.name }}</span>
                            </router-link>
                        </div>
                    </div>
                </nav>

                <!-- Divider -->
                <hr class="my-4 border-gray-200 dark:border-gray-700">

                <!-- Quick Links (Not logged in) -->
                <div v-if="!authStore.isAuthenticated" class="space-y-2">
                    <router-link
                        to="/login"
                        class="block w-full px-4 py-3 text-center text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                        @click="showMobileMenu = false"
                    >
                        Log In
                    </router-link>
                    <router-link
                        to="/register"
                        class="block w-full px-4 py-3 text-center text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all"
                        @click="showMobileMenu = false"
                    >
                        Sign Up
                    </router-link>
                </div>

                <!-- User Quick Actions (Logged in) -->
                <div v-else class="space-y-2">
                    <router-link
                        to="/student/courses"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        My Courses
                    </router-link>
                    <router-link
                        to="/wishlist"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Wishlist
                    </router-link>
                    <router-link
                        to="/messages"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Messages
                    </router-link>
                    <router-link
                        to="/notifications"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Notifications
                    </router-link>
                    <router-link
                        to="/student/bootcamps"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-.658.953l-4.5 2.276a1 1 0 01-.894 0l-4.5-2.276A1 1 0 0110 15.382V8.618a1 1 0 01.658-.953l4.5-2.276z" />
                        </svg>
                        My Bootcamps
                    </router-link>
                    <router-link
                        to="/student/ebooks"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        My Ebooks
                    </router-link>
                    <router-link
                        to="/student/purchase-history"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Purchase History
                    </router-link>
                    <router-link
                        to="/student/support"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-colors"
                        @click="showMobileMenu = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 12.708m0 5.656l3.536-3.536M12.708 5.636L9.172 9.172m5.656 0l-3.536 3.536M4 4l4 4M21 4l-4 4M4 20l4-4M21 20l-4-4" />
                        </svg>
                        Customer Support
                    </router-link>

                    <hr class="my-2 border-gray-200 dark:border-gray-700">

                    <button
                        @click="handleLogout; showMobileMenu = false"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Log Out
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Footer -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>© 2026 Eduvora</span>
                    <div class="flex gap-3">
                        <a v-for="social in mobileSocialLinks" :key="social.name" :href="social.url" target="_blank" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                            <component :is="social.icon" class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, h } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useCartStore } from '../../stores/cart';
import { useNotificationStore } from '../../stores/notification';
import { categoryApi } from '../../services/api';

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const notificationStore = useNotificationStore();
const cartStore = useCartStore();
const notificationStore = useNotificationStore();
const cartStore = useCartStore();
const notificationStore = useNotificationStore();

const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const notificationStore = useNotificationStore();

const router = useRouter();
const authStore = useAuthStore();

// State
const searchQuery = ref('');
const userMenuOpen = ref(false);
const activeMegaMenu = ref(null);
const showMobileSearch = ref(false);
const showMobileMenu = ref(false);
const isScrolled = ref(false);
const categories = ref([]);

// Computed
const cartCount = computed(() => cartStore.items.length);
const wishlistCount = computed(() => authStore.user?.wishlist_count || 0);
const unreadNotificationsCount = computed(() => notificationStore.unreadCount);
const unreadMessagesCount = computed(() => authStore.unread_messages_count || 0);
const enrolledCount = computed(() => authStore.user?.enrolled_courses_count || 0);

const dashboardLink = computed(() => {
    if (authStore.isAdmin) return '/admin';
    if (authStore.isInstructor) return '/instructor';
    return '/student';
});

const profileLink = computed(() => {
    if (authStore.isAdmin) return '/admin/profile';
    if (authStore.isInstructor) return '/instructor/profile';
    return '/student/profile';
});

const roleBadgeClass = computed(() => {
    const role = authStore.user?.role?.toLowerCase();
    const classes = {
        admin: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        instructor: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        student: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    };
    return classes[role] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
});

// Social icons for mobile footer (simple SVG render functions)
const mobileSocialLinks = [
    {
        name: 'Twitter',
        icon: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
                h('path', { d: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' })
            ])
        },
        url: 'https://twitter.com/eduvora',
    },
    {
        name: 'Facebook',
        icon: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
                h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' })
            ])
        },
        url: 'https://facebook.com/eduvora',
    },
    {
        name: 'Instagram',
        icon: {
            render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
                h('path', { d: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z' })
            ])
        },
        url: 'https://instagram.com/eduvora',
    },
];

// Navigation items
const navItems = computed(() => [
    { name: 'Home', path: '/' },
    {
        name: 'Courses',
        path: '/courses',
        children: categories.value
            .filter(c => c.show_in_menu && c.parent_id === null)
            .slice(0, 8)
            .map(cat => ({
                ...cat,
                children: cat.children?.filter(child => child.show_in_menu) || [],
            })),
    },
    { name: 'Bootcamp', path: '/bootcamps' },
    { name: 'Bundles', path: '/bundles' },
    {
        name: 'More',
        path: '#',
        children: [
            { name: 'Ebooks', path: '/ebooks' },
            { name: 'Find a Tutor', path: '/tutors' },
        ],
    },
]);

// Methods
function toggleMegaMenu(name) {
    activeMegaMenu.value = activeMegaMenu.value === name ? null : name;
}

function handleSearch() {
    if (searchQuery.value.trim()) {
        router.push({ path: '/courses', query: { search: searchQuery.value } });
        searchQuery.value = '';
        showMobileSearch.value = false;
    }
}

async function handleLogout() {
    try {
        // Include device token if exists
        const token = localStorage.getItem('device_token');
        if (token) {
            await authStore.logout({ user_agent: token });
        } else {
            await authStore.logout();
        }
        router.push('/');
        userMenuOpen.value = false;
    } catch (error) {
        console.error('Logout error:', error);
    }
}

async function fetchCategories() {
    try {
        const { data } = await categoryApi.getAll();
        const allCategories = data.data || [];

        // Build tree: group by parent_id
        const categoriesMap = {};
        const tree = [];

        // First pass: create map
        allCategories.forEach(cat => {
            categoriesMap[cat.id] = { ...cat, children: [] };
        });

        // Second pass: build tree
        allCategories.forEach(cat => {
            if (cat.parent_id && categoriesMap[cat.parent_id]) {
                categoriesMap[cat.parent_id].children.push(categoriesMap[cat.id]);
            } else {
                tree.push(categoriesMap[cat.id]);
            }
        });

        // Sort by sort_order or name
        tree.sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0) || a.name.localeCompare(b.name));
        tree.forEach(cat => {
            cat.children.sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0) || a.name.localeCompare(b.name));
        });

        categories.value = tree;
    } catch (error) {
        console.error('Error fetching categories:', error);
        categories.value = [];
    }
}

function handleScroll() {
    isScrolled.value = window.scrollY > 20;
}

function closeMenusOnClickOutside(e) {
    if (!e.target.closest('.relative')) {
        activeMegaMenu.value = null;
        userMenuOpen.value = false;
    }
}

// Lifecycle
onMounted(async () => {
    await fetchCategories();
    window.addEventListener('scroll', handleScroll);
    document.addEventListener('click', closeMenusOnClickOutside);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    document.removeEventListener('click', closeMenusOnClickOutside);
});
</script>

<style scoped>
/* Pulse animation for badges once */
.animate-pulse-once {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) 1;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.1); }
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Hide scrollbar for Chrome, Safari and Opera */
.mega-dropdown-menu::-webkit-scrollbar {
    width: 6px;
}
.mega-dropdown-menu::-webkit-scrollbar-track {
    background: transparent;
}
.mega-dropdown-menu::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}
</style>
