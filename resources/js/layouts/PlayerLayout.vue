<template>
    <div class="min-h-screen bg-gray-900 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 border-r border-gray-700 flex-shrink-0">
            <div class="p-4 border-b border-gray-700">
                <router-link to="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 011.788 0l7 3a1 1 0 000 1.838l-7 3a1 1 0 01-.788 0l7-3a1 1 0 110-1.838l-4 1.714a.999.999 0 01-.356.257L11 6.217V1.283a1 1 0 00-1.052-.18L6.75 2.183A1 1 0 005.683 3H1.283a1 1 0 000 2.163l5.25 2.261a1 1 0 001.052.18v3.574a.999.999 0 01-.175.537l-3 1.635a1 1 0 01-.788 0l-3-1.635a.999.999 0 01-.175-.537V6.217a1 1 0 001.052-.18l5.25-2.261a1 1 0 000-1.838z"/>
                        </svg>
                    </div>
                    <span class="text-white font-semibold">Eduvora</span>
                </router-link>
            </div>
            
            <nav class="p-4 space-y-2">
                <router-link :to="`/student/courses/${courseId}/learn`" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </router-link>
                
                <router-link :to="`/student/courses/${courseId}/learn` + '/curriculum'" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Curriculum</span>
                </router-link>
                
                <router-link :to="`/student/courses/${courseId}/learn` + '/notes'" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Notes</span>
                </router-link>
                
                <router-link :to="`/student/courses/${courseId}/learn` + '/discussions'" 
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Discussions</span>
                </router-link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <header class="h-14 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-white font-medium">{{ courseTitle }}</h1>
                </div>
                
                <div class="flex items-center gap-4">
                    <button class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 15.879l-4.878-3.126a.5.5 0 01-.252-.438V7.124a.5.5 0 01.252-.438l4.878-3.125a.5.5 0 01.694 0l4.878 3.126a.5.5 0 01.252.438v5.31a.5.5 0 01-.252.438l-4.878 3.126a.5.5 0 01-.694 0z" />
                        </svg>
                    </button>
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm">{{ userInitials }}</span>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 p-6 overflow-auto">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const authStore = useAuthStore();

const sidebarOpen = ref(true);
const courseId = computed(() => route.params.id);
const courseTitle = computed(() => 'Course Player'); // TODO: fetch from store
const userInitials = computed(() => {
    const name = authStore.user?.name || 'U';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});
</script>