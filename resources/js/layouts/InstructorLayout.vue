<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <InstructorSidebar :collapsed="sidebarCollapsed" @toggle="sidebarCollapsed = !sidebarCollapsed" />

        <div class="transition-all duration-300" :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">
            <InstructorTopbar @toggle-sidebar="sidebarCollapsed = !sidebarCollapsed" />

            <main class="p-6">
                <!-- Breadcrumbs -->
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <router-link to="/instructor" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414l7-7a1 1 0 000-1.414zM3 10a1 1 0 011-1h.01a1 1 0 110 2H4a1 1 0 01-1-1z" />
                                </svg>
                                Dashboard
                            </router-link>
                        </li>
                        <li v-for="crumb in breadcrumbs" :key="crumb.path">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <router-link :to="crumb.path" class="ml-1 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 md:ml-2">
                                    {{ crumb.name }}
                                </router-link>
                            </div>
                        </li>
                    </ol>
                </nav>

                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import InstructorSidebar from '../components/layout/InstructorSidebar.vue';
import InstructorTopbar from '../components/layout/InstructorTopbar.vue';

const route = useRoute();
const sidebarCollapsed = ref(false);

const breadcrumbs = computed(() => {
    const path = route.path.replace('/instructor/', '').split('/').filter(Boolean);
    const items = [];

    let currentPath = '/instructor';
    path.forEach((segment) => {
        currentPath += `/${segment}`;
        items.push({
            name: segment.charAt(0).toUpperCase() + segment.slice(1).replace(/-/g, ' '),
            path: currentPath
        });
    });

    return items.slice(0, -1);
});
</script>
