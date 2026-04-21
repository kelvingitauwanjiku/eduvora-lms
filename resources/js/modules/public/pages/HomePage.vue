<template>
    <div>
        <!-- Hero Section with HeroBanner -->
        <HeroBanner 
            title="Learn Without Limits"
            subtitle="Unlock your potential with expert-led courses. Join thousands of learners transforming their careers every day."
            search-placeholder="What do you want to learn?"
            :show-stats="true"
            :stats="stats"
        />

        <!-- Featured Courses -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Featured</span>
                        <h2 class="text-3xl font-bold mt-1">Popular Courses</h2>
                        <p class="text-gray-500 mt-1">Handpicked courses to kickstart your learning journey</p>
                    </div>
                    <router-link to="/courses" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition">
                        View all
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </router-link>
                </div>
                <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="i in 4" :key="i" class="bg-gray-100 rounded-2xl h-80 animate-pulse"></div>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <CourseCard 
                        v-for="course in featuredCourses" 
                        :key="course.id" 
                        :course="course"
                        :wishlist-items="wishlistItems"
                        :cart-items="cartItems"
                        @wishlist-toggle="handleWishlistToggle"
                        @add-to-cart="handleAddToCart"
                    />
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Explore</span>
                    <h2 class="text-3xl font-bold mt-1">Browse by Category</h2>
                    <p class="text-gray-500 mt-2">Find the perfect course in your area of interest</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <CategoryCard 
                        v-for="category in categories" 
                        :key="category.id" 
                        :category="category"
                    />
                </div>
            </div>
        </section>

        <!-- What Makes Us Different -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Why Us</span>
                    <h2 class="text-3xl font-bold mt-1">What Makes Us Different</h2>
                    <p class="text-gray-500 mt-2">We provide the best learning experience</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="feature in features" :key="feature.title" class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-500 text-sm">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Instructors -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Learn from the best</span>
                        <h2 class="text-3xl font-bold mt-1">Top Instructors</h2>
                        <p class="text-gray-500 mt-1">Learn from industry experts</p>
                    </div>
                    <router-link to="/instructors" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition">
                        View all
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </router-link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="instructor in topInstructors" :key="instructor.id" class="bg-white rounded-2xl p-6 text-center border border-gray-100 hover:shadow-lg transition">
                        <img :src="instructor.avatar || '/images/avatar.png'" :alt="instructor.name" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="font-semibold">{{ instructor.name }}</h3>
                        <p class="text-sm text-gray-500 mb-3">{{ instructor.title || 'Instructor' }}</p>
                        <div class="flex items-center justify-center gap-4 text-sm text-gray-500">
                            <span>{{ instructor.total_students || 0 }} students</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                {{ instructor.rating || '0.0' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Testimonials</span>
                    <h2 class="text-3xl font-bold mt-1">What Our Students Say</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="testimonial in testimonials" :key="testimonial.id" class="bg-gray-50 rounded-2xl p-6">
                        <div class="flex items-center gap-1 mb-4">
                            <svg v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 mb-4">"{{ testimonial.content }}"</p>
                        <div class="flex items-center gap-3">
                            <img :src="testimonial.avatar || '/images/avatar.png'" :alt="testimonial.name" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="font-semibold text-sm">{{ testimonial.name }}</p>
                                <p class="text-xs text-gray-500">{{ testimonial.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900">50K+</div>
                        <div class="text-gray-500 mt-1">Active Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900">2,500+</div>
                        <div class="text-gray-500 mt-1">Online Courses</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900">500+</div>
                        <div class="text-gray-500 mt-1">Expert Instructors</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900">150+</div>
                        <div class="text-gray-500 mt-1">Schools & Universities</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Signup -->
        <section class="py-20 bg-gradient-to-r from-blue-600 to-indigo-600 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC40Ij48cGF0aCBkPSJNMzYgMzRjMC0yLjIwLTEuOC00LTQtNHMtNCAxLjgtNCA0IDEuOC00IDQtNCA0IDEuOCA0IDQtNHptMCAxMGMwLTIuMjAtMS44LTQtNC00cy00IDEuOC00IDQgMS44IDQgNCA0IDEuOCA0IDQtNHoiLz48L2c+PC9nPjwvc3ZnPg==')]"></div>
            </div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Stay Updated with Our Newsletter</h2>
                <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">Get the latest courses, learning tips, and exclusive offers delivered to your inbox.</p>
                <form @submit.prevent="subscribeNewsletter" class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
                    <input 
                        v-model="newsletterEmail"
                        type="email" 
                        placeholder="Enter your email address"
                        required
                        class="flex-1 px-6 py-4 text-gray-900 bg-white rounded-xl focus:outline-none focus:ring-2 focus:ring-white"
                    >
                    <button 
                        type="submit" 
                        :disabled="subscribing"
                        class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-gray-100 transition shadow-lg disabled:opacity-70"
                    >
                        {{ subscribing ? 'Subscribing...' : 'Subscribe' }}
                    </button>
                </form>
                <p v-if="subscribed" class="text-green-300 mt-3">Thank you for subscribing!</p>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 bg-gray-900 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC40Ij48cGF0aCBkPSJNMzYgMzRjMC0yLjIwLTEuOC00LTQtNHMtNCAxLjgtNCA0IDEuOC00IDQtNCA0IDEuOCA0IDQtNHptMCAxMGMwLTIuMjAtMS44LTQtNC00cy00IDEuOC00IDQgMS44IDQgNCA0IDEuOCA0IDQtNHoiLz48L2c+PC9nPjwvc3ZnPg==')]"></div>
            </div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Start Your Learning Journey?</h2>
                <p class="text-gray-400 text-lg mb-8 max-w-2xl mx-auto">Join thousands of students already learning on Eduvora. Start your free trial today.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <router-link to="/register" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-600/20 transition">
                        Start Free Trial
                    </router-link>
                    <router-link to="/courses" class="px-8 py-4 border-2 border-gray-600 text-white font-semibold rounded-xl hover:bg-gray-800 transition">
                        Browse Courses
                    </router-link>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { courseApi, categoryApi } from '@/services/api';
import HeroBanner from '../../../components/public/HeroBanner.vue';
import CourseCard from '../../../components/public/CourseCard.vue';
import CategoryCard from '../../../components/public/CategoryCard.vue';

const newsletterEmail = ref('');
const subscribing = ref(false);
const subscribed = ref(false);

async function subscribeNewsletter() {
    if (!newsletterEmail.value) return;
    subscribing.value = true;
    try {
        await new Promise(resolve => setTimeout(resolve, 1000));
        subscribed.value = true;
        newsletterEmail.value = '';
    } catch (error) {
        console.error('Error subscribing:', error);
    } finally {
        subscribing.value = false;
    }
}

const featuredCourses = ref([]);
const categories = ref([]);
const topInstructors = ref([]);
const loading = ref(true);
const wishlistItems = ref([]);
const cartItems = ref([]);

const stats = {
    students: '50K',
    courses: '2,500',
    instructors: '500',
    rating: '4.8'
};

const features = [
    {
        title: 'Expert Instructors',
        description: 'Learn from industry professionals with years of experience.',
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'
    },
    {
        title: 'Certificate', 
        description: 'Earn recognized certificates upon course completion.',
        icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'
    },
    {
        title: 'Lifetime Access',
        description: 'Access courses anytime, anywhere, forever.',
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
    },
    {
        title: 'Interactive Learning',
        description: 'Engage with quizzes, assignments, and projects.',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z'
    }
];

const testimonials = [
    {
        id: 1,
        name: 'Sarah Johnson',
        role: 'Software Developer',
        content: 'The courses helped me transition from a beginner to a professional developer. Highly recommended!',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100'
    },
    {
        id: 2,
        name: 'Michael Chen',
        role: 'Data Scientist',
        content: 'Eduvora has the best instructors and course content. The certificate opened many doors for me.',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100'
    },
    {
        id: 3,
        name: 'Emily Davis',
        role: 'UX Designer',
        content: 'The UI/UX design courses are fantastic. I landed my dream job after completing the bootcamp.',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100'
    }
];

onMounted(async () => {
    try {
        const [featuredRes, categoriesRes] = await Promise.all([
            courseApi.getFeatured().catch(() => ({ data: { data: [] } })),
            categoryApi.getAll().catch(() => ({ data: { data: [] } })),
        ]);
        featuredCourses.value = featuredRes.data?.data || [];
        categories.value = categoriesRes.data?.data || [];
        
        topInstructors.value = [
            { id: 1, name: 'John Smith', title: 'Web Development Expert', total_students: 5000, rating: '4.9' },
            { id: 2, name: 'Lisa Anderson', title: 'Data Science Lead', total_students: 4200, rating: '4.8' },
            { id: 3, name: 'David Wilson', title: 'Mobile App Developer', total_students: 3800, rating: '4.7' },
            { id: 4, name: 'Anna Martinez', title: 'AI/ML Engineer', total_students: 3500, rating: '4.9' },
        ];
    } catch (error) {
        console.error('Error loading home data:', error);
    } finally {
        loading.value = false;
    }
});

function handleWishlistToggle(courseId) {
    const index = wishlistItems.value.indexOf(courseId);
    if (index > -1) {
        wishlistItems.value.splice(index, 1);
    } else {
        wishlistItems.value.push(courseId);
    }
}

function handleAddToCart(courseId) {
    if (!cartItems.value.includes(courseId)) {
        cartItems.value.push(courseId);
    }
}
</script>