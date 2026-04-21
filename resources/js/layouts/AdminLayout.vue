<template>
    <div class="min-h-screen bg-gray-50/50">
        <AdminSidebar 
            :collapsed="sidebarCollapsed" 
            @toggle="sidebarCollapsed = !sidebarCollapsed"
            @close="sidebarOpen = false"
        />
        
        <!-- Mobile Overlay -->
        <div v-if="sidebarOpen" 
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"
             @click="sidebarOpen = false">
        </div>
        
        <div class="transition-all duration-300" :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">
            <AdminTopbar 
                @toggle-sidebar="sidebarOpen = !sidebarOpen"
                @toggle-search="searchOpen = !searchOpen"
            />
            
            <main class="p-6 pt-4">
                <router-view />
            </main>
        </div>

        <!-- Global Search Modal -->
        <Teleport to="body">
            <div v-if="searchOpen" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-screen items-start justify-center p-4 pt-20">
                    <div class="fixed inset-0 bg-black/50 transition-opacity" @click="searchOpen = false"></div>
                    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                        <div class="p-4 border-b border-gray-100">
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input 
                                    type="text" 
                                    v-model="searchQuery"
                                    placeholder="Search courses, students, instructors..."
                                    class="w-full pl-12 pr-4 py-3 text-lg border-0 focus:outline-none focus:ring-0"
                                    @keydown.escape="searchOpen = false"
                                >
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto p-4">
                            <div v-if="!searchQuery" class="text-center py-8">
                                <p class="text-gray-500">Start typing to search...</p>
                            </div>
                            <div v-else-if="searchResults.length" class="space-y-2">
                                <div v-for="result in searchResults" :key="result.id" 
                                     class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition"
                                     @click="goToResult(result)">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <component :is="result.icon" class="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ result.title }}</p>
                                        <p class="text-sm text-gray-500">{{ result.subtitle }}</p>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ result.type }}</span>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <p class="text-gray-500">No results found</p>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-100 bg-gray-50">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center gap-4">
                                    <span class="flex items-center gap-1">
                                        <kbd class="px-2 py-1 bg-white rounded border shadow-sm">Enter</kbd>
                                        <span>to select</span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <kbd class="px-2 py-1 bg-white rounded border shadow-sm">Esc</kbd>
                                        <span>to close</span>
                                    </span>
                                </div>
                                <span class="flex items-center gap-1">
                                    <kbd class="px-2 py-1 bg-white rounded border shadow-sm">↑↓</kbd>
                                    <span>to navigate</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Notifications Panel -->
        <Teleport to="body">
            <div v-if="notificationsOpen" class="fixed inset-0 z-40" @click="notificationsOpen = false"></div>
        </Teleport>

        <!-- Profile Dropdown -->
        <Teleport to="body">
            <div v-if="profileOpen" class="fixed inset-0 z-40" @click="profileOpen = false"></div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import AdminSidebar from '../components/layout/AdminSidebar.vue';
import AdminTopbar from '../components/layout/AdminTopbar.vue';

const sidebarCollapsed = ref(false);
const sidebarOpen = ref(false);
const searchOpen = ref(false);
const notificationsOpen = ref(false);
const profileOpen = ref(false);
const searchQuery = ref('');

const searchResults = computed(() => {
    if (!searchQuery.value) return [];
    
    const query = searchQuery.value.toLowerCase();
    const items = [
        { id: 1, title: 'Web Development Course', subtitle: 'Created by John Smith', type: 'Course', icon: 'IconCourses', path: '/admin/courses' },
        { id: 2, title: 'John Smith', subtitle: 'john@example.com', type: 'Instructor', icon: 'IconUsers', path: '/admin/instructors' },
        { id: 3, title: 'Sarah Johnson', subtitle: 'sarah@example.com', type: 'Student', icon: 'IconUsers', path: '/admin/students' },
        { id: 4, title: 'Programming Category', subtitle: '24 courses', type: 'Category', icon: 'IconCategories', path: '/admin/categories' },
        { id: 5, title: '50% OFF Coupon', subtitle: 'Valid until Dec 31', type: 'Coupon', icon: 'IconCoupons', path: '/admin/coupons' },
    ];
    
    return items.filter(item => 
        item.title.toLowerCase().includes(query) || 
        item.subtitle.toLowerCase().includes(query)
    ).slice(0, 8);
});

function goToResult(result) {
    searchOpen.value = false;
    window.location.href = result.path;
}
</script>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-down {
    animation: slideDown 0.2s ease-out;
}
</style>