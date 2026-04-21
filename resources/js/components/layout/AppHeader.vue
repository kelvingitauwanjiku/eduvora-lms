<template>
    <header class="sticky top-0 z-40 border-b border-gray-200/80 dark:border-gray-700/80 bg-white/90 dark:bg-gray-950/90 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-3 h-16">
                <!-- Mobile menu + Logo -->
                <div class="flex items-center gap-2 lg:gap-8 min-w-0">
                    <button
                        type="button"
                        class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                        aria-label="Open menu"
                        :aria-expanded="mobileOpen"
                        @click="mobileOpen = true"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>

                    <router-link :to="navbarBrand.homePath" class="flex items-center gap-2 shrink-0" @click="closeAll">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <span class="text-white font-bold text-lg">E</span>
                        </div>
                        <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate">{{ navbarBrand.name }}</span>
                    </router-link>

                    <!-- Desktop nav -->
                    <nav class="hidden lg:flex items-center gap-0.5" aria-label="Main">
                        <router-link
                            to="/"
                            class="px-3 py-2 text-sm font-medium rounded-lg transition"
                            :class="isActive('/') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800/80'"
                            @click="closeAll"
                        >
                            Home
                        </router-link>

                        <!-- Courses mega -->
                        <div ref="coursesMegaWrap" class="relative">
                            <button
                                type="button"
                                class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1"
                                :class="activeMega === 'courses' ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800/80'"
                                aria-haspopup="true"
                                :aria-expanded="activeMega === 'courses'"
                                @click="toggleMega('courses')"
                            >
                                Courses
                                <svg class="w-4 h-4 transition-transform" :class="activeMega === 'courses' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-150"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-1"
                            >
                                <div
                                    v-if="activeMega === 'courses'"
                                    class="absolute left-0 top-full mt-1 w-[min(42rem,calc(100vw-2rem))] max-w-[90vw] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-5 z-50"
                                    role="menu"
                                >
                                    <div class="grid sm:grid-cols-2 gap-2 max-h-[min(24rem,60vh)] overflow-y-auto pr-1">
                                        <router-link
                                            v-for="cat in megaCategories"
                                            :key="cat.id"
                                            :to="categoryTo(cat)"
                                            class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/80 transition group"
                                            role="menuitem"
                                            @click="closeAll"
                                        >
                                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/40 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/60 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition truncate">{{ cat.name }}</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ cat.courses_count ?? 0 }} courses</p>
                                            </div>
                                        </router-link>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <router-link
                                            to="/courses"
                                            class="flex items-center justify-center gap-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                                            @click="closeAll"
                                        >
                                            View all courses
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        </router-link>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <router-link
                            v-for="link in navbarSimpleLinks"
                            :key="link.name"
                            :to="link.to"
                            :title="link.title"
                            class="px-3 py-2 text-sm font-medium rounded-lg transition"
                            :class="isActive(link.to) ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800/80'"
                            @click="closeAll"
                        >
                            {{ link.name }}
                        </router-link>

                        <!-- More mega -->
                        <div ref="moreMegaWrap" class="relative">
                            <button
                                type="button"
                                class="px-3 py-2 text-sm font-medium rounded-lg transition flex items-center gap-1"
                                :class="activeMega === 'more' ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800/80'"
                                aria-haspopup="true"
                                :aria-expanded="activeMega === 'more'"
                                @click="toggleMega('more')"
                            >
                                More
                                <svg class="w-4 h-4 transition-transform" :class="activeMega === 'more' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-150"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-1"
                            >
                                <div
                                    v-if="activeMega === 'more'"
                                    class="absolute right-0 top-full mt-1 w-80 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 p-2 z-50"
                                    role="menu"
                                >
                                    <router-link
                                        v-for="item in navbarMoreLinks"
                                        :key="item.name"
                                        :to="moreLinkTo(item)"
                                        class="flex flex-col gap-0.5 px-3 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/80 transition"
                                        role="menuitem"
                                        @click="closeAll"
                                    >
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ item.name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ item.description }}</span>
                                    </router-link>
                                </div>
                            </transition>
                        </div>
                    </nav>
                </div>

                <!-- Search + actions -->
                <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                    <!-- Desktop search -->
                    <form class="relative hidden lg:block" @submit.prevent="runSearch">
                        <input
                            v-model="searchQuery"
                            type="search"
                            autocomplete="off"
                            placeholder="Search courses…"
                            aria-label="Search courses"
                            class="w-56 xl:w-72 pl-10 pr-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:bg-white dark:focus:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                        >
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </form>

                    <!-- Mobile compact search -->
                    <div ref="mobileSearchWrap" class="lg:hidden relative">
                        <button
                            type="button"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            :aria-expanded="mobileSearchOpen"
                            aria-label="Search"
                            @click="mobileSearchOpen = !mobileSearchOpen"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                        <div
                            v-if="mobileSearchOpen"
                            class="absolute right-0 top-full mt-2 w-[min(18rem,calc(100vw-2rem))] rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 p-2 shadow-xl z-50"
                        >
                            <form class="flex gap-2" @submit.prevent="runSearch(); mobileSearchOpen = false">
                                <input v-model="searchQuery" type="search" placeholder="Search…" class="flex-1 min-w-0 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <button type="submit" class="px-3 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white">Go</button>
                            </form>
                        </div>
                    </div>

                    <!-- Cart (LMS: always visible; guests → login with redirect) -->
                    <router-link
                        :to="cartLinkTo"
                        class="relative p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        :title="authStore.isAuthenticated ? 'Cart' : 'Sign in to view cart'"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span
                            v-if="cartCount > 0"
                            class="absolute top-0.5 right-0.5 min-w-[1.125rem] h-4 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none"
                        >
                            {{ cartCount > 9 ? '9+' : cartCount }}
                        </span>
                    </router-link>

                    <router-link
                        v-if="authStore.isAuthenticated"
                        to="/student/wishlist"
                        class="hidden sm:block relative p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        title="Wishlist"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        <span
                            v-if="wishlistCount > 0"
                            class="absolute top-0.5 right-0.5 min-w-[1.125rem] h-4 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none"
                        >
                            {{ wishlistCount > 9 ? '9+' : wishlistCount }}
                        </span>
                    </router-link>

                    <router-link
                        v-if="authStore.isAuthenticated"
                        to="/student/notifications"
                        class="hidden sm:block relative p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                        title="Notifications"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    </router-link>

                    <template v-if="authStore.isAuthenticated">
                        <div ref="userMenuWrap" class="relative">
                            <button
                                type="button"
                                class="flex items-center gap-2 p-1 sm:pr-3 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition border border-transparent hover:border-gray-200 dark:hover:border-gray-600"
                                :aria-expanded="userMenuOpen"
                                aria-haspopup="true"
                                @click="toggleUserMenu"
                            >
                                <img :src="authStore.user?.avatar || authStore.user?.photo || '/images/avatar.png'" alt="" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700">
                                <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-200 max-w-[7rem] truncate">{{ authStore.user?.name }}</span>
                                <svg class="hidden sm:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-100"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div
                                    v-if="userMenuOpen"
                                    class="absolute right-0 mt-2 w-64 sm:w-72 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50 overflow-hidden"
                                    role="menu"
                                >
                                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ authStore.user?.email }}</p>
                                        <p v-if="authStore.user?.role" class="text-[11px] uppercase tracking-wide text-blue-600 dark:text-blue-400 mt-1">{{ authStore.user.role }}</p>
                                    </div>
                                    <router-link :to="dashboardLink" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800" role="menuitem" @click="closeAll">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                        Dashboard
                                    </router-link>
                                    <router-link v-if="!authStore.isAdmin" to="/student/courses" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800" role="menuitem" @click="closeAll">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        My courses
                                    </router-link>
                                    <router-link v-if="!authStore.isAdmin" to="/student/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800" role="menuitem" @click="closeAll">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        My profile
                                    </router-link>
                                    <router-link v-if="!authStore.isAdmin" to="/student/wishlist" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 sm:hidden" role="menuitem" @click="closeAll">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                        Wishlist
                                    </router-link>
                                    <router-link v-if="!authStore.isAdmin" to="/student/messages" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800" role="menuitem" @click="closeAll">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                        Messages
                                    </router-link>
                                    <router-link v-if="!authStore.isAdmin" to="/student/purchases" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800" role="menuitem" @click="closeAll">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Purchase history
                                    </router-link>
                                    <router-link v-if="!authStore.isAdmin" to="/student/support" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800" role="menuitem" @click="closeAll">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        Support
                                    </router-link>
                                    <button type="button" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 border-t border-gray-100 dark:border-gray-700 mt-1" @click="handleLogout">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Log out
                                    </button>
                                </div>
                            </transition>
                        </div>
                    </template>

                    <template v-else>
                        <router-link to="/login" class="hidden sm:inline text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-2 py-2" @click="closeAll">
                            Log in
                        </router-link>
                        <router-link to="/register" class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:shadow-lg hover:shadow-blue-600/25 transition" @click="closeAll">
                            Sign up
                        </router-link>
                    </template>
                </div>
            </div>
        </div>

        <!-- Mobile drawer (LMS offcanvas) -->
        <teleport to="body">
            <transition name="fade">
                <div
                    v-if="mobileOpen"
                    class="fixed inset-0 z-[100] lg:hidden"
                    aria-modal="true"
                    role="dialog"
                    aria-label="Main navigation"
                >
                    <div class="absolute inset-0 bg-black/50" @click="mobileOpen = false" />
                    <aside
                        class="absolute left-0 top-0 h-full w-[min(22rem,100%)] bg-white dark:bg-gray-950 shadow-2xl flex flex-col border-r border-gray-200 dark:border-gray-800"
                    >
                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800">
                            <span class="font-bold text-gray-900 dark:text-white">{{ navbarBrand.name }}</span>
                            <button type="button" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300" aria-label="Close menu" @click="mobileOpen = false">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4 space-y-1">
                            <template v-if="authStore.isAuthenticated">
                                <div class="flex items-center gap-3 pb-4 mb-2 border-b border-gray-200 dark:border-gray-800">
                                    <img :src="authStore.user?.avatar || authStore.user?.photo || '/images/avatar.png'" alt="" class="w-12 h-12 rounded-full object-cover">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ authStore.user?.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ authStore.user?.email }}</p>
                                    </div>
                                </div>
                                <router-link :to="dashboardLink" class="block py-3 px-3 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium" @click="mobileOpen = false">Dashboard</router-link>
                                <router-link v-if="!authStore.isAdmin" to="/student/courses" class="block py-3 px-3 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800" @click="mobileOpen = false">My courses</router-link>
                                <router-link v-if="!authStore.isAdmin" to="/student/wishlist" class="block py-3 px-3 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800" @click="mobileOpen = false">Wishlist</router-link>
                            </template>
                            <router-link to="/" class="block py-3 px-3 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800" @click="mobileOpen = false">Home</router-link>
                            <button
                                type="button"
                                class="flex w-full items-center justify-between py-3 px-3 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 text-left font-medium"
                                @click="mobileCoursesOpen = !mobileCoursesOpen"
                            >
                                Courses
                                <svg class="w-4 h-4 transition-transform" :class="mobileCoursesOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div v-show="mobileCoursesOpen" class="pl-2 border-l-2 border-gray-100 dark:border-gray-800 ml-3 space-y-0.5">
                                <router-link
                                    v-for="cat in mobileDrawerCategories"
                                    :key="cat.id"
                                    :to="categoryTo(cat)"
                                    class="block py-2 px-2 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400"
                                    @click="mobileOpen = false"
                                >
                                    {{ cat.name }}
                                </router-link>
                                <router-link to="/courses" class="block py-2 px-2 text-sm font-medium text-blue-600 dark:text-blue-400" @click="mobileOpen = false">All courses</router-link>
                            </div>
                            <router-link
                                v-for="link in navbarSimpleLinks"
                                :key="'m-' + link.name"
                                :to="link.to"
                                class="block py-3 px-3 rounded-lg text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800"
                                @click="mobileOpen = false"
                            >
                                {{ link.name }}
                            </router-link>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider pt-4 px-3">More</p>
                            <router-link
                                v-for="item in navbarMoreLinks"
                                :key="'mm-' + item.name"
                                :to="moreLinkTo(item)"
                                class="block py-2.5 px-3 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm"
                                @click="mobileOpen = false"
                            >
                                {{ item.name }}
                            </router-link>
                        </div>
                        <div v-if="!authStore.isAuthenticated" class="p-4 border-t border-gray-200 dark:border-gray-800 space-y-2">
                            <router-link to="/login" class="block w-full text-center py-3 rounded-xl border border-gray-200 dark:border-gray-600 font-medium text-gray-800 dark:text-gray-100" @click="mobileOpen = false">Log in</router-link>
                            <router-link to="/register" class="block w-full text-center py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold" @click="mobileOpen = false">Sign up</router-link>
                        </div>
                    </aside>
                </div>
            </transition>
        </teleport>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { categoryApi, cartApi, wishlistApi } from '../../services/api';
import { navbarBrand, navbarSimpleLinks, navbarMoreLinks, navbarCoursesMega } from '../../config/navbar';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const searchQuery = ref('');
const userMenuOpen = ref(false);
const activeMega = ref(null);
const cartCount = ref(0);
const wishlistCount = ref(0);
const categories = ref([]);
const mobileOpen = ref(false);
const mobileCoursesOpen = ref(true);
const mobileSearchOpen = ref(false);

const coursesMegaWrap = ref(null);
const moreMegaWrap = ref(null);
const userMenuWrap = ref(null);
const mobileSearchWrap = ref(null);

const megaCategories = computed(() => categories.value.slice(0, navbarCoursesMega.maxCategories));
const mobileDrawerCategories = computed(() => categories.value.slice(0, 30));

const dashboardLink = computed(() => {
    if (authStore.isAdmin) return '/admin';
    if (authStore.isInstructor) return '/instructor';
    return '/student';
});

const cartLinkTo = computed(() =>
    authStore.isAuthenticated ? '/student/cart' : { path: '/login', query: { redirect: '/student/cart' } },
);

function isActive(path) {
    if (path === '/') return route.path === '/';
    return route.path === path || route.path.startsWith(`${path}/`);
}

function categoryTo(cat) {
    return { path: '/courses', query: { category: String(cat.id) } };
}

function moreLinkTo(item) {
    const q = item.query && Object.keys(item.query).length ? item.query : {};
    return { path: item.to, query: q };
}

function toggleMega(name) {
    userMenuOpen.value = false;
    activeMega.value = activeMega.value === name ? null : name;
}

function toggleUserMenu() {
    activeMega.value = null;
    userMenuOpen.value = !userMenuOpen.value;
}

function closeAll() {
    activeMega.value = null;
    userMenuOpen.value = false;
    mobileSearchOpen.value = false;
}

function runSearch() {
    const q = searchQuery.value.trim();
    if (q) {
        router.push({ path: '/search', query: { q } });
        closeAll();
    }
}

async function handleLogout() {
    closeAll();
    await authStore.logout();
    router.push('/');
}

async function fetchCategories() {
    try {
        const { data } = await categoryApi.getAll();
        const list = data.data || [];
        categories.value = list.map((c) => ({
            ...c,
            courses_count: c.courses_count ?? c.courses?.length ?? 0,
        }));
    } catch (e) {
        console.error('Navbar categories:', e);
    }
}

async function fetchCounts() {
    if (!authStore.isAuthenticated) {
        cartCount.value = 0;
        wishlistCount.value = 0;
        return;
    }
    try {
        const [cartRes, wishRes] = await Promise.allSettled([cartApi.get(), wishlistApi.get()]);
        if (cartRes.status === 'fulfilled') {
            const items = cartRes.value.data?.data ?? [];
            cartCount.value = Array.isArray(items) ? items.length : 0;
        }
        if (wishRes.status === 'fulfilled') {
            const w = wishRes.value.data?.data ?? [];
            wishlistCount.value = Array.isArray(w) ? w.length : 0;
        }
    } catch {
        /* ignore */
    }
}

function onDocClick(e) {
    const t = e.target;
    if (!(t instanceof Node)) return;
    if (coursesMegaWrap.value?.contains(t) || moreMegaWrap.value?.contains(t)) return;
    if (userMenuWrap.value?.contains(t)) return;
    if (mobileSearchOpen.value && mobileSearchWrap.value && !mobileSearchWrap.value.contains(t)) {
        mobileSearchOpen.value = false;
    }
    activeMega.value = null;
    userMenuOpen.value = false;
}

function onKeydown(e) {
    if (e.key === 'Escape') {
        closeAll();
        mobileOpen.value = false;
        mobileSearchOpen.value = false;
    }
}

watch(() => route.fullPath, () => {
    mobileOpen.value = false;
    closeAll();
});

watch(() => authStore.isAuthenticated, () => {
    fetchCounts();
});

watch(mobileOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

onMounted(() => {
    fetchCategories();
    fetchCounts();
    document.addEventListener('click', onDocClick, true);
    document.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    document.removeEventListener('click', onDocClick, true);
    document.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
