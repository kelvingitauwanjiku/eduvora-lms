<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <router-link :to="homeRoute"
                             class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 inline-flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414l7-7a1 1 0 000-1.414zM3 10a1 1 0 011-1h.01a1 1 0 110 2H4a1 1 0 01-1-1z" />
                    </svg>
                    {{ homeLabel }}
                </router-link>
            </li>
            <li v-for="(crumb, index) in items" :key="crumb.path">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <router-link :to="crumb.path"
                                 class="ml-1 text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 md:ml-2 transition-colors"
                                 :class="index === items.length - 1 ? 'text-gray-700 dark:text-gray-200 font-medium cursor-default hover:text-blue-600 dark:hover:text-blue-400' : ''">
                        {{ crumb.name }}
                    </router-link>
                </div>
            </li>
        </ol>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
    homeLabel: {
        type: String,
        default: 'Home'
    }
});

const route = useRoute();

const homeRoute = computed(() => {
    const path = route.path.split('/')[1];
    if (path === 'admin') return '/admin';
    if (path === 'instructor') return '/instructor';
    if (path === 'student') return '/student';
    return '/';
});

const items = computed(() => {
    const path = route.path.split('/').slice(2).filter(Boolean);
    const items = [];
    let currentPath = '/' + route.path.split('/')[1];

    path.forEach((segment) => {
        currentPath += `/${segment}`;
        items.push({
            name: segment.charAt(0).toUpperCase() + segment.slice(1).replace(/-/g, ' '),
            path: currentPath
        });
    });

    return items;
});
</script>
