<template>
    <header class="h-16 bg-white/80 backdrop-blur-lg border-b border-gray-200/50 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 shadow-sm transition-all duration-300"
            :class="{ 'lg:pl-20': !sidebarCollapsed, 'lg:pl-6': sidebarCollapsed }">
        <!-- Left: Menu Toggle & Breadcrumb -->
        <div class="flex items-center gap-3">
            <button @click="$emit('toggle-sidebar')"
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Page Title on mobile -->
            <div class="lg:hidden">
                <h1 class="text-lg font-bold text-gray-900 dark:text-white truncate max-w-[200px]">{{ pageTitle }}</h1>
            </div>

            <!-- Desktop Breadcrumb -->
            <nav class="hidden lg:flex items-center gap-2 text-sm">
                <router-link to="/admin" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">Home</router-link>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 dark:text-white font-medium">{{ pageTitle }}</span>
            </nav>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">
            <!-- View Site Link (from old system) -->
            <a :href="frontendUrl" target="_blank"
               class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span class="hidden xl:inline">{{ get_phrase('View site') }}</span>
            </a>

            <!-- Global Search -->
            <button @click="$emit('toggle-search')"
                    class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200 hidden md:flex">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <!-- Help Center Dropdown (from old system) -->
            <div class="relative">
                <button @click="helpOpen = !helpOpen"
                        class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200"
                        :title="get_phrase('Help Center')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>

                <transition name="dropdown">
                    <div v-if="helpOpen" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                        <div class="p-3 border-b border-gray-100 dark:border-gray-700">
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">{{ get_phrase('Help center') }}</h5>
                        </div>
                        <div class="p-2">
                            <a :href="docsUrl" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ get_phrase('Read documentation') }}
                            </a>
                            <a :href="videoUrl" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ get_phrase('Watch video tutorial') }}
                            </a>
                            <a :href="supportUrl" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ get_phrase('Get customer support') }}
                            </a>
                            <a :href="supportUrl" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.992 1.992 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ get_phrase('Order customization') }}
                            </a>
                            <a :href="featuresUrl" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 110 4m0-4a2 2 0 110 4m-6 8a2 2 0 110-4m0 4a2 2 0 110-4m0 4v2m0-6V4m0 2a2 2 0 110 4m0-4a2 2 0 110 4" />
                                </svg>
                                {{ get_phrase('Request a new feature') }}
                            </a>
                            <a :href="servicesUrl" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-all font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ get_phrase('Get Services') }}
                                <svg class="w-3 h-3 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l.07.189c.92 2.383.182 3.663-.238 4.671a2.426 2.426 0 01-1.028 1.271c-.906.454-1.917.523-2.676.238l-.189-.07c-.921-.302-1.603.92-1.902 0l-.07-.189c-.92-2.383-.182-3.663.238-4.671a2.426 2.426 0 011.028-1.271c.906-.454 1.917-.523 2.676-.238l.189.07z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- School Switcher Dropdown -->
            <div class="relative hidden md:block">
                <button @click="schoolOpen = !schoolOpen"
                        class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden xl:block">{{ currentSchool?.name || 'Main Campus' }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <transition name="dropdown">
                    <div v-if="schoolOpen" class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Switch School</p>
                        </div>
                        <div class="p-2 max-h-60 overflow-y-auto">
                            <button v-for="school in schools" :key="school.id"
                                    @click="selectSchool(school)"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                                    :class="currentSchool?.id === school.id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                     :class="school.bgColor">
                                    <span class="text-white font-bold text-sm">{{ school.initials }}</span>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ school.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ school.location }}</p>
                                </div>
                                <svg v-if="currentSchool?.id === school.id" class="w-5 h-5 text-blue-500 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Notifications -->
            <NotificationDropdown />

            <!-- Messages -->
            <button class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200 relative hidden md:flex">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span v-if="unreadMessages > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-blue-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                    {{ unreadMessages > 9 ? '9+' : unreadMessages }}
                </span>
            </button>

            <!-- Divider -->
            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 mx-2 hidden md:block"></div>

            <!-- Profile Dropdown -->
            <ProfileDropdown />
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import NotificationDropdown from './NotificationDropdown.vue';
import ProfileDropdown from './ProfileDropdown.vue';

defineEmits(['toggle-sidebar', 'toggle-search']);

const route = useRoute();
const authStore = useAuthStore();

const schoolOpen = ref(false);
const helpOpen = ref(false);
const sidebarCollapsed = ref(false);

const frontendUrl = window.location.origin;
const docsUrl = 'https://creativeitem.com/docs/academy-lms';
const videoUrl = 'https://www.youtube.com/watch?v=-HHhJUGQPeU&list=PLR1GrQCi5Zqvhh7wgtt-ShMAM1RROYJgE';
const supportUrl = 'https://support.creativeitem.com';
const featuresUrl = 'https://support.creativeitem.com';
const servicesUrl = 'https://creativeitem.com/services';

function get_phrase(key) {
  const phrases = {
    'View site': 'View site',
    'Help center': 'Help center',
    'Read documentation': 'Read documentation',
    'Watch video tutorial': 'Watch video tutorial',
    'Get customer support': 'Get customer support',
    'Order customization': 'Order customization',
    'Request a new feature': 'Request a new feature',
    'Get Services': 'Get Services'
  };
  return phrases[key] || key;
}

const currentSchool = computed(() => authStore.user?.school);

const schools = ref([
    { id: 1, name: 'Main Campus', location: 'New York, USA', initials: 'MC', bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600' },
    { id: 2, name: 'West Campus', location: 'Los Angeles, USA', initials: 'WC', bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600' },
    { id: 3, name: 'East Campus', location: 'Chicago, USA', initials: 'EC', bgColor: 'bg-gradient-to-br from-green-500 to-green-600' },
]);

const unreadMessages = ref(3);

function selectSchool(school) {
    // Implementation for school switching
    if (!import.meta?.env?.PROD) console.log('Switched to school:', school.id);
    schoolOpen.value = false;
}

const pageTitle = computed(() => {
    const titles = {
        '/admin': 'Dashboard',
        '/admin/users': 'Users',
        '/admin/instructors': 'Instructors',
        '/admin/courses': 'Courses',
        '/admin/categories': 'Categories',
        '/admin/enrollments': 'Enrollments',
        '/admin/blogs': 'Blog',
        '/admin/bundles': 'Course Bundles',
        '/admin/ebooks': 'Ebooks',
        '/admin/bootcamps': 'Bootcamps',
        '/admin/tutor-booking': 'Tutor Booking',
        '/admin/payment-history': 'Payment History',
        '/admin/revenue': 'Revenue',
        '/admin/coupons': 'Coupons',
        '/admin/payouts': 'Payouts',
        '/admin/support': 'Support',
        '/admin/newsletter': 'Newsletter',
        '/admin/contacts': 'Contacts',
        '/admin/reports': 'Reports',
        '/admin/settings': 'Settings',
        '/admin/profile': 'My Profile',
        '/admin/notifications': 'Notifications',
    };
    return titles[route.path] || 'Admin Panel';
});
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
