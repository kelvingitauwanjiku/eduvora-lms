<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Instructor Dashboard</h1>
            <p class="mt-1 text-gray-500">Welcome back, {{ authStore.user?.name }}!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Earnings</p>
                        <p class="text-2xl font-bold">${{ formatNumber(stats.total_earnings) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0-1v1m0-1H9m3 1h.01" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ stats.earnings_change || 0 }}% from last month</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Students</p>
                        <p class="text-2xl font-bold">{{ formatNumber(stats.total_students) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ stats.students_change || 0 }} new this month</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Courses</p>
                        <p class="text-2xl font-bold">{{ stats.total_courses }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ stats.published_courses }} published</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Avg Rating</p>
                        <p class="text-2xl font-bold">{{ stats.avg_rating || '0.0' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ stats.total_reviews || 0 }} reviews</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Revenue Overview</h2>
                    <select v-model="chartPeriod" @change="fetchRevenueChart" class="text-sm border-gray-200 rounded-lg px-3 py-1">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
                <div class="h-64">
                    <LineChart v-if="revenueChartData.labels.length" :data="revenueChartData" />
                    <div v-else class="flex items-center justify-center h-full text-gray-400">No data available</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h2>
                <div class="space-y-3">
                    <router-link to="/instructor/courses/create" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Create Course</p>
                            <p class="text-xs text-gray-500">Add a new course</p>
                        </div>
                    </router-link>
                    <router-link to="/instructor/quizzes/create" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Create Quiz</p>
                            <p class="text-xs text-gray-500">Build a new quiz</p>
                        </div>
                    </router-link>
                    <router-link to="/instructor/live-classes/create" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Schedule Live</p>
                            <p class="text-xs text-gray-500">Host a live class</p>
                        </div>
                    </router-link>
                    <router-link to="/instructor/notice-board" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Post Notice</p>
                            <p class="text-xs text-gray-500">Announce to students</p>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Enrollments</h2>
                    <router-link to="/instructor/students" class="text-sm text-blue-600 hover:text-blue-700">View all</router-link>
                </div>
                <div v-if="loadingEnrollments" class="space-y-4">
                    <div v-for="i in 5" :key="i" class="animate-pulse flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-32 mb-1"></div>
                            <div class="h-3 bg-gray-200 rounded w-24"></div>
                        </div>
                    </div>
                </div>
                <div v-else-if="recentEnrollments.length === 0" class="text-center py-8 text-gray-500">
                    No recent enrollments
                </div>
                <div v-else class="space-y-4">
                    <div v-for="enrollment in recentEnrollments" :key="enrollment.id" class="flex items-center gap-4 p-2 hover:bg-gray-50 rounded-lg transition">
                        <img :src="enrollment.user?.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ enrollment.user?.name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ enrollment.course?.title }}</p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">{{ formatDate(enrollment.created_at) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Reviews</h2>
                    <router-link to="/instructor/courses" class="text-sm text-blue-600 hover:text-blue-700">View all</router-link>
                </div>
                <div v-if="loadingReviews" class="space-y-4">
                    <div v-for="i in 5" :key="i" class="animate-pulse flex items-start gap-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-32 mb-1"></div>
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                        </div>
                    </div>
                </div>
                <div v-else-if="recentReviews.length === 0" class="text-center py-8 text-gray-500">
                    No recent reviews
                </div>
                <div v-else class="space-y-4">
                    <div v-for="review in recentReviews" :key="review.id" class="flex items-start gap-4 p-2 hover:bg-gray-50 rounded-lg transition">
                        <img :src="review.user?.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ review.user?.name }}</span>
                                <div class="flex">
                                    <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ review.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">My Courses</h2>
                <router-link to="/instructor/courses" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    View All
                </router-link>
            </div>
            <div v-if="loadingCourses" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="i in 3" :key="i" class="animate-pulse border border-gray-200 rounded-xl p-4">
                    <div class="w-full h-32 bg-gray-200 rounded-lg mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
            </div>
            <div v-else-if="courses.length === 0" class="text-center py-8">
                <p class="text-gray-500 mb-4">You haven't created any courses yet</p>
                <router-link to="/instructor/courses/create" class="text-blue-600 hover:text-blue-700 font-medium">
                    Create your first course
                </router-link>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <router-link v-for="course in courses.slice(0, 6)" :key="course.id" :to="`/instructor/courses/${course.id}/edit`" 
                    class="group border border-gray-200 rounded-xl p-4 hover:border-blue-500 hover:shadow-md transition">
                    <div class="relative mb-4">
                        <img :src="course.thumbnail || '/images/course-placeholder.jpg'" class="w-full h-32 rounded-lg object-cover">
                        <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full"
                            :class="course.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            {{ course.status || 'draft' }}
                        </span>
                    </div>
                    <h3 class="font-medium text-gray-900 line-clamp-1 group-hover:text-blue-600">{{ course.title }}</h3>
                    <div class="flex items-center justify-between mt-2 text-sm text-gray-500">
                        <span>{{ course.total_enrolled || 0 }} students</span>
                        <span>${{ course.price || 0 }}</span>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { instructorApi, courseApi } from '@/services/api';
import LineChart from '@/components/ui/LineChart.vue';

const authStore = useAuthStore();

const loadingEnrollments = ref(true);
const loadingReviews = ref(true);
const loadingCourses = ref(true);
const chartPeriod = ref('30');

const stats = ref({
    total_earnings: 0,
    total_students: 0,
    total_courses: 0,
    avg_rating: '0.0',
    earnings_change: 0,
    students_change: 0,
    published_courses: 0,
    total_reviews: 0,
});

const revenueChartData = reactive({
    labels: [],
    datasets: [{
        label: 'Revenue',
        data: [],
        borderColor: '#3B82F6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        fill: true,
        tension: 0.4,
    }]
});

const recentEnrollments = ref([]);
const recentReviews = ref([]);
const courses = ref([]);

function formatNumber(num) {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num;
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

async function fetchDashboard() {
    try {
        const { data } = await instructorApi.getDashboard();
        stats.value = {
            total_earnings: data.total_earnings || 0,
            total_students: data.total_students || 0,
            total_courses: data.total_courses || 0,
            avg_rating: data.avg_rating || '0.0',
            earnings_change: data.earnings_change || 0,
            students_change: data.students_change || 0,
            published_courses: data.published_courses || 0,
            total_reviews: data.total_reviews || 0,
        };
        
        if (data.recent_enrollments) {
            recentEnrollments.value = data.recent_enrollments;
        }
        if (data.recent_reviews) {
            recentReviews.value = data.recent_reviews;
        }
    } catch (error) {
        console.error('Error fetching dashboard:', error);
    } finally {
        loadingEnrollments.value = false;
        loadingReviews.value = false;
    }
}

async function fetchRevenueChart() {
    try {
        const { data } = await instructorApi.getRevenue({ days: chartPeriod.value });
        revenueChartData.labels = data.labels || [];
        revenueChartData.datasets[0].data = data.values || [];
    } catch (error) {
        console.error('Error fetching revenue chart:', error);
    }
}

async function fetchCourses() {
    try {
        const { data } = await instructorApi.getCourses({ per_page: 6 });
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
    } finally {
        loadingCourses.value = false;
    }
}

onMounted(() => {
    fetchDashboard();
    fetchRevenueChart();
    fetchCourses();
});
</script>
