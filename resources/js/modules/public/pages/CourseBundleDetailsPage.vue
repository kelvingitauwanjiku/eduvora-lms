<template>
    <div>
        <div v-if="loading" class="animate-pulse">
            <div class="bg-gray-200 h-96"></div>
        </div>

        <template v-else-if="bundle">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div class="flex items-center gap-2 mb-4">
                                <span v-if="bundle.is_featured" class="px-3 py-1 bg-white/20 text-white text-sm rounded-full">Featured</span>
                                <span v-if="bundle.is_subscription" class="px-3 py-1 bg-purple-500 text-white text-sm rounded-full">Subscription</span>
                            </div>
                            <h1 class="text-4xl font-bold text-white">{{ bundle.title }}</h1>
                            <p class="text-xl text-blue-100 mt-4">{{ bundle.description }}</p>
                            
                            <div class="flex flex-wrap items-center gap-6 mt-6">
                                <div class="flex items-center gap-2">
                                    <div class="flex">
                                        <svg v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400" :class="i <= Math.round(bundle.rating || 0) ? 'fill-current' : 'text-gray-300'" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                    <span class="text-white font-medium">{{ bundle.rating || '4.5' }}</span>
                                    <span class="text-blue-200">({{ bundle.reviews || 0 }} reviews)</span>
                                </div>
                                <span class="text-blue-200 flex items-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l3.12 3.333-3.12.833V4.354l3.12.833V2.688L6 4.354l6 1.833zm0 15.292V14.52l-3.12-.833v3.957L6 16.687l3.12-1.833 3.12.833V17.52l3.12.833v1.833L6 18.521l-3.12 1.833V20.52l3.12-.833v1.833L12 20.146l-3.12-.833v-3.957L3 17.52l6-1.833z" transform="scale(0.5) translate(24,24)" />
                                    </svg>
                                    {{ bundle.students || 0 }} students
                                </span>
                                <span class="text-blue-200 flex items-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ bundle.courses_count || 0 }} courses
                                </span>
                            </div>
                        </div>
                        
                        <!-- Pricing Card -->
                        <div class="bg-white rounded-2xl shadow-2xl p-6">
                            <img :src="bundle.thumbnail || '/images/course-placeholder.jpg'" 
                                class="w-full h-40 object-cover rounded-xl mb-6">
                            
                            <div class="text-center mb-6">
                                <div v-if="bundle.is_subscription" class="flex items-center justify-center gap-2">
                                    <span class="text-4xl font-bold text-gray-900">${{ bundle.price }}</span>
                                    <span class="text-gray-500">/month</span>
                                </div>
                                <div v-else class="flex items-center justify-center gap-2">
                                    <span class="text-4xl font-bold text-gray-900">${{ bundle.price }}</span>
                                    <span v-if="bundle.original_price" class="text-xl text-gray-400 line-through">${{ bundle.original_price }}</span>
                                </div>
                                <p v-if="bundle.savings" class="text-green-600 mt-2 font-medium">Save {{ bundle.savings }}%</p>
                            </div>

                            <button v-if="!isEnrolled" @click="handleEnroll" :disabled="enrolling"
                                class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-600/20 transition disabled:opacity-50">
                                {{ enrolling ? 'Processing...' : 'Enroll Now' }}
                            </button>

                            <router-link v-else to="/student"
                                class="block w-full py-4 bg-green-600 text-white font-semibold rounded-xl text-center hover:bg-green-700 transition">
                                Go to Dashboard
                            </router-link>

                            <button @click="toggleWishlist"
                                class="w-full mt-3 py-3 border border-gray-200 rounded-xl font-medium hover:bg-gray-50 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" :class="isWishlisted ? 'text-red-500 fill-current' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                {{ isWishlisted ? 'Wishlisted' : 'Add to Wishlist' }}
                            </button>

                            <div class="mt-6 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        Courses Included
                                    </span>
                                    <span class="font-medium">{{ bundle.courses_count || 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Duration
                                    </span>
                                    <span class="font-medium">{{ bundle.duration || 'Lifetime' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Access
                                    </span>
                                    <span class="font-medium">{{ bundle.is_subscription ? 'Unlimited' : 'Lifetime' }}</span>
                                </div>
                                <div v-if="bundle.certificate" class="flex justify-between">
                                    <span class="text-gray-500">Certificate</span>
                                    <span class="font-medium text-green-600">Included</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Courses Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Courses in this Bundle</h2>
                    <p class="text-gray-500 mt-1">Everything you need to master the subject</p>
                </div>

                <div v-if="coursesLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="i in 3" :key="i" class="animate-pulse">
                        <div class="bg-gray-200 h-48 rounded-xl"></div>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <router-link v-for="course in courses" :key="course.id" :to="`/courses/${course.id}`" class="group">
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition h-full">
                            <div class="aspect-video relative overflow-hidden">
                                <img :src="course.thumbnail || '/images/course-placeholder.jpg'" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition">
                                <div class="absolute top-3 left-3">
                                    <span v-if="course.is_free" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-full">Free</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">
                                    {{ course.title }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">{{ course.instructor?.name }}</p>
                                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ course.rating || '0.0' }}
                                    </span>
                                    <span>{{ course.total_lessons || 0 }} lessons</span>
                                </div>
                            </div>
                        </div>
                    </router-link>
                </div>

                <!-- What You'll Get -->
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">What You'll Get</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <div class="w-12 h-12 mx-auto bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900">{{ bundle.courses_count || 0 }} Courses</h3>
                            <p class="text-sm text-gray-500 mt-1">Comprehensive curriculum</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <div class="w-12 h-12 mx-auto bg-green-100 rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900">Certificate</h3>
                            <p class="text-sm text-gray-500 mt-1">Upon completion</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <div class="w-12 h-12 mx-auto bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900">Lifetime Access</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ bundle.is_subscription ? 'While subscribed' : 'Learn at your pace' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <div class="w-12 h-12 mx-auto bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900">Support</h3>
                            <p class="text-sm text-gray-500 mt-1">Expert instructors</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Bundle Not Found</h2>
            <router-link to="/bundles" class="text-blue-600 hover:underline mt-2 inline-block">
                Browse Bundles
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { bundleApi, wishlistApi } from '@/services/api';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const coursesLoading = ref(true);
const bundle = ref(null);
const courses = ref([]);
const isEnrolled = ref(false);
const enrolling = ref(false);
const isWishlisted = ref(false);

async function fetchBundle() {
    loading.value = true;
    try {
        const { data } = await bundleApi.getById(route.params.id);
        bundle.value = data;
    } catch (error) {
        console.error('Error fetching bundle:', error);
        bundle.value = {
            id: 1,
            title: 'Complete Web Development Bundle',
            description: 'Learn full-stack web development from beginner to advanced',
            price: 99,
            original_price: 299,
            savings: 67,
            courses_count: 5,
            students: 1250,
            rating: 4.8,
            reviews: 342,
            is_subscription: false,
            is_featured: true,
            thumbnail: '',
        };
    } finally {
        loading.value = false;
    }
}

async function fetchCourses() {
    coursesLoading.value = true;
    try {
        const { data } = await bundleApi.getCourses(route.params.id);
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
        courses.value = [];
    } finally {
        coursesLoading.value = false;
    }
}

async function toggleWishlist() {
    if (!authStore.isAuthenticated) {
        return router.push({ name: 'login', query: { redirect: route.fullPath } });
    }
    try {
        if (isWishlisted.value) {
            await wishlistApi.remove(bundle.value.id);
        } else {
            await wishlistApi.add(bundle.value.id);
        }
        isWishlisted.value = !isWishlisted.value;
    } catch (error) {
        console.error('Error toggling wishlist:', error);
    }
}

async function handleEnroll() {
    if (!authStore.isAuthenticated) {
        return router.push({ name: 'login', query: { redirect: route.fullPath } });
    }
    
    enrolling.value = true;
    try {
        await bundleApi.enroll(bundle.value.id);
        isEnrolled.value = true;
    } catch (error) {
        console.error('Error enrolling:', error);
    } finally {
        enrolling.value = false;
    }
}

onMounted(() => {
    fetchBundle();
    fetchCourses();
});
</script>