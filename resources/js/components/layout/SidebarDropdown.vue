<template>
    <div class="relative">
        <button @click="isOpen = !isOpen"
                class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group"
                :class="isActive ? 'bg-gradient-to-r from-blue-500/20 to-indigo-500/20 text-white border-l-4 border-blue-500 shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5'">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
                </svg>
                <span class="text-sm font-medium">{{ title }}</span>
            </div>
            <svg class="w-4 h-4 transition-transform duration-200 group-hover:text-white" :class="isOpen ? 'rotate-180 text-white' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <transition name="dropdown">
            <div v-if="isOpen" class="ml-8 mt-1 space-y-1 border-l border-white/10 pl-3">
                <router-link 
                    v-for="route in routeList" 
                    :key="route.path"
                    :to="`/admin/${route.path}`"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all duration-200 group"
                    :class="$route.path.includes(route.path) ? 'bg-blue-500/20 text-blue-400 font-medium' : 'text-gray-400 hover:text-white hover:bg-white/5'">
                    <span class="w-1.5 h-1.5 rounded-full transition-colors" :class="$route.path.includes(route.path) ? 'bg-blue-500' : 'bg-gray-600 group-hover:bg-gray-500'"></span>
                    {{ route.name }}
                </router-link>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';

const props = defineProps({
    title: String,
    icon: String,
    routes: Array,
    collapsed: Boolean
});

const route = useRoute();
const isOpen = ref(false);

const routeList = computed(() => {
    const routeNames = {
        'courses': { name: 'All Courses', path: 'courses' },
        'categories': { name: 'Categories', path: 'categories' },
        'blogs': { name: 'Blog Posts', path: 'blogs' },
        'bundles': { name: 'Course Bundles', path: 'bundles' },
        'ebooks': { name: 'Ebooks', path: 'ebooks' },
        'users': { name: 'All Users', path: 'users' },
        'instructors': { name: 'Instructors', path: 'instructors' },
        'students': { name: 'Students', path: 'students' },
        'bootcamps': { name: 'Bootcamps', path: 'bootcamps' },
        'tutor-booking': { name: 'Tutor Booking', path: 'tutor-booking' },
        'enrollments': { name: 'Enrollments', path: 'enrollments' },
        'payment-history': { name: 'Payment History', path: 'payment-history' },
        'revenue': { name: 'Revenue', path: 'revenue' },
        'coupons': { name: 'Coupons', path: 'coupons' },
        'payouts': { name: 'Payouts', path: 'payouts' },
        'support': { name: 'Support Tickets', path: 'support' },
        'newsletter': { name: 'Newsletter', path: 'newsletter' },
        'contacts': { name: 'Contacts', path: 'contacts' },
        'reports': { name: 'Reports', path: 'reports' },
        'settings': { name: 'General Settings', path: 'settings' },
        'languages': { name: 'Languages', path: 'languages' },
        'certificates': { name: 'Certificates', path: 'certificates' },
        'notifications': { name: 'Notification Settings', path: 'notifications' },
        'knowledge-base': { name: 'Knowledge Base', path: 'knowledge-base' },
        'quizzes': { name: 'All Quizzes', path: 'quizzes' },
        'assignments': { name: 'Assignments', path: 'assignments' },
        'live-classes': { name: 'Live Classes', path: 'live-classes' },
    };
    
    return props.routes.map(r => routeNames[r] || { name: r, path: r });
});

const isActive = computed(() => {
    return props.routes.some(r => route.path.includes(r));
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
    transform: translateY(-5px);
}
</style>
