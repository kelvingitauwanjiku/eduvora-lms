<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ authStore.user?.name }}!</h1>
            <p class="mt-1 text-gray-500">Continue your learning journey</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">My Courses</p>
                        <p class="text-2xl font-bold">{{ stats.my_courses }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Certificates</p>
                        <p class="text-2xl font-bold">{{ stats.certificates }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.241-.247 3.47 3.47 0 014.438 0 3.42 3.42 0 001.241.247 3.42 3.42 0 012.814.013 3.47 3.47 0 012.814.013 3.42 3.42 0 001.241.247c.85.215 1.525.85 1.741 1.764 1.166.49 1.914 1.597 1.914 2.855 0 .912-.485 1.743-1.278 2.197a3.42 3.42 0 00-.506 1.837"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Wishlist</p>
                        <p class="text-2xl font-bold">{{ stats.wishlist }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Hours Learned</p>
                        <p class="text-2xl font-bold">{{ stats.hours }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Continue Learning</h2>
                <router-link to="/student/courses" class="text-sm text-blue-600 hover:text-blue-700">View all</router-link>
            </div>

            <div v-if="loading && enrolledCourses.length === 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="i in 3" :key="i" class="animate-pulse flex items-center gap-4 p-4 border border-gray-200 rounded-xl">
                    <div class="w-20 h-14 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1">
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-1.5 bg-gray-200 rounded-full"></div>
                    </div>
                </div>
            </div>

            <div v-else-if="enrolledCourses.length === 0" class="text-center py-8">
                <p class="text-gray-500 mb-4">You haven't enrolled in any courses yet</p>
                <router-link to="/courses" class="text-blue-600 hover:text-blue-700 font-medium">Browse Courses</router-link>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <router-link v-for="course in enrolledCourses.slice(0, 3)" :key="course.id" :to="`/student/courses/${course.id}/learn`" class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:border-blue-500 transition">
                    <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-20 h-14 rounded-lg object-cover">
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900 line-clamp-1">{{ course.title }}</h3>
                        <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" :style="{ width: (course.progress || 0) + '%' }"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ course.progress || 0 }}% complete</p>
                    </div>
                    <button class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        Continue
                    </button>
                </router-link>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Recommended Courses</h2>
                <router-link to="/courses" class="text-sm text-blue-600 hover:text-blue-700">Browse all</router-link>
            </div>

            <div v-if="recommendedCourses.length === 0" class="text-center py-8">
                <p class="text-gray-500">No recommendations available</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <router-link v-for="course in recommendedCourses.slice(0, 4)" :key="course.id" :to="`/courses/${course.id}`" class="group">
                    <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition">
                        <div class="aspect-video overflow-hidden">
                            <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-full object-cover group-hover:scale-105 transition">
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition text-sm">{{ course.title }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ course.instructor?.name }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-xs">{{ course.rating || '0.0' }}</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ course.total_enrolled || 0 }} students</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span v-if="course.is_free" class="text-sm font-bold text-green-600">Free</span>
                                <span v-else class="text-sm font-bold text-gray-900">${{ course.price }}</span>
                                <button @click.prevent="addToWishlist(course.id)" class="p-1 text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Recent Activity</h2>
                <div v-if="recentActivity.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No recent activity</p>
                </div>
                <div v-else class="space-y-4">
                    <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="activity.bg">
                            <svg class="w-5 h-5" :class="activity.color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="activity.icon" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ activity.title }}</p>
                            <p class="text-xs text-gray-500">{{ activity.time }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Links</h2>
                <div class="space-y-2">
                    <router-link v-for="link in quickLinks" :key="link.path" :to="link.path" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="link.icon" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700">{{ link.name }}</span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { enrollmentApi, courseApi, wishlistApi } from '@/services/api';

const authStore = useAuthStore();

const loading = ref(true);
const stats = ref({ my_courses: 0, certificates: 0, wishlist: 0, hours: 0 });
const enrolledCourses = ref([]);
const recommendedCourses = ref([]);

const recentActivity = ref([]);

const quickLinks = [
    { name: 'My Courses', path: '/student/courses', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
    { name: 'Cart', path: '/student/cart', icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.427 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' },
    { name: 'Wishlist', path: '/student/wishlist', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
    { name: 'Support', path: '/student/support', icon: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z' },
];

async function fetchDashboard() {
    try {
        loading.value = true;
        const [enrollmentsRes, coursesRes] = await Promise.all([
            enrollmentApi.getAll(),
            courseApi.getFeatured()
        ]);
        enrolledCourses.value = enrollmentsRes.data.data || [];
        recommendedCourses.value = coursesRes.data.data || [];
        stats.value.my_courses = enrolledCourses.value.length;
        stats.value.wishlist = 0;
        stats.value.certificates = 0;
        stats.value.hours = enrolledCourses.value.reduce((acc, c) => acc + (c.duration || 0), 0);
        
        recentActivity.value = [
            { id: 1, title: 'Welcome to Eduvora!', time: 'Just now', icon: 'M5 13l4 4L19 7', bg: 'bg-green-100', color: 'text-green-600' },
        ];
    } catch (error) {
        console.error('Error fetching dashboard:', error);
    } finally {
        loading.value = false;
    }
}

async function addToWishlist(courseId) {
    try {
        await wishlistApi.add(courseId);
        stats.value.wishlist++;
    } catch (error) {
        console.error('Error adding to wishlist:', error);
    }
}

onMounted(() => {
    fetchDashboard();
});
</script>
