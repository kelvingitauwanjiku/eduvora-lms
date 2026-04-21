<template>
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-indigo-900 via-indigo-800 to-purple-900 text-white transition-all duration-300 min-h-screen hidden lg:block sticky top-0 shadow-xl">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-white/10">
                <router-link to="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">E</span>
                    </div>
                    <span class="text-lg font-bold">Instructor</span>
                </router-link>
            </div>

            <!-- Collapse Button -->
            <button @click="$emit('toggle')"
                    class="absolute -right-3 top-20 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 group"
                    :class="collapsed ? 'rotate-180' : ''">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Navigation -->
            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)] scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent">
                <!-- Dashboard -->
                <router-link to="/instructor"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path === '/instructor' ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Dashboard</span>
                </router-link>

                <!-- Main Menu Section -->
                <div v-if="!collapsed" class="px-4 py-2 mt-4">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-white/50">Main Menu</span>
                </div>

                <!-- My Courses -->
                <router-link to="/instructor/courses"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/course') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">My Courses</span>
                </router-link>

                <!-- Create Course -->
                <router-link to="/instructor/create"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path === '/instructor/create' ? 'bg-white/20 text-white border-l-4 border-green-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Create Course</span>
                </router-link>

                <!-- Curriculum -->
                <router-link to="/instructor/curriculum"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/curriculum') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Curriculum</span>
                </router-link>

                <!-- Quizzes -->
                <router-link to="/instructor/quizzes"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/quiz') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Quizzes</span>
                </router-link>

                <!-- Assignments -->
                <router-link to="/instructor/assignments"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/assignment') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Assignments</span>
                </router-link>

                <!-- Notice Board -->
                <router-link to="/instructor/notice-board"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/notice') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Notice Board</span>
                </router-link>

                <!-- Live Classes -->
                <router-link to="/instructor/live-classes"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/live') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Live Classes</span>
                </router-link>

                <!-- Students -->
                <router-link to="/instructor/students"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/student') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Students</span>
                </router-link>

                <!-- Finance Section -->
                <div v-if="!collapsed" class="px-4 py-2 mt-4">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-white/50">Finance</span>
                </div>

                <!-- Revenue -->
                <router-link to="/instructor/revenue"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/revenue') ? 'bg-white/20 text-white border-l-4 border-green-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 1v1m0-1H9m3 1h.01" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Revenue</span>
                </router-link>

                <!-- Payouts -->
                <router-link to="/instructor/payouts"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/payout') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H7a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Payouts</span>
                </router-link>

                <!-- Profile -->
                <router-link to="/instructor/profile"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
                             :class="$route.path.startsWith('/instructor/profile') ? 'bg-white/20 text-white border-l-4 border-blue-400' : 'text-white/70 hover:text-white hover:bg-white/10'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span v-if="!collapsed" class="text-sm font-medium">Profile</span>
                </router-link>
            </nav>
        </aside>

        <!-- Mobile Menu Overlay -->
        <div v-if="mobileMenuOpen" class="lg:hidden fixed inset-0 bg-black/50 z-40" @click="mobileMenuOpen = false">
            <div class="w-64 bg-gradient-to-b from-indigo-900 to-purple-900 h-full p-4" @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <span class="text-lg font-bold text-white">Instructor Menu</span>
                    <button @click="mobileMenuOpen = false" class="p-2 hover:bg-white/10 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="space-y-1">
                    <router-link v-for="item in navItems" :key="item.path" :to="item.path"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-white/70 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        <span class="text-sm font-medium">{{ item.name }}</span>
                    </router-link>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="pt-16 lg:pt-0">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({ collapsed: Boolean });
defineEmits(['toggle']);

const mobileMenuOpen = ref(false);
</script>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>
