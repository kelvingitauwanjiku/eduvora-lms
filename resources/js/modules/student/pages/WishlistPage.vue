<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Wishlist</h1>
            <p class="mt-1 text-gray-500">Courses you've saved for later</p>
        </div>

        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="i in 6" :key="i" class="animate-pulse">
                <div class="bg-gray-200 h-48 rounded-xl"></div>
                <div class="mt-4 h-5 bg-gray-200 rounded w-3/4"></div>
            </div>
        </div>

        <div v-else-if="wishlist.length === 0" class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Your wishlist is empty</h3>
            <p class="text-gray-500 mt-1">Save courses you're interested in</p>
            <router-link to="/courses" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Browse Courses
            </router-link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="course in wishlist" :key="course.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition duration-300">
                <router-link :to="`/courses/${course.id}`">
                    <div class="aspect-video relative overflow-hidden">
                        <img :src="course.thumbnail || '/images/course-placeholder.jpg'" :alt="course.title"
                            class="w-full h-full object-cover">
                    </div>
                </router-link>
                <div class="p-5">
                    <router-link :to="`/courses/${course.id}`" class="font-semibold text-gray-900 hover:text-blue-600 line-clamp-2">
                        {{ course.title }}
                    </router-link>
                    <p class="text-sm text-gray-500 mt-1">{{ course.instructor?.name }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ course.rating || '0.0' }}</span>
                        </div>
                        <span class="text-gray-300">|</span>
                        <span class="text-sm text-gray-500">{{ course.total_enrolled || 0 }} students</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span v-if="course.is_free" class="text-xl font-bold text-green-600">Free</span>
                        <span v-else class="text-xl font-bold text-gray-900">${{ course.price }}</span>
                        <div class="flex gap-2">
                            <button @click="addToCart(course.id)" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                Add to Cart
                            </button>
                            <button @click="removeFromWishlist(course.id)" class="p-1.5 text-gray-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { wishlistApi, cartApi } from '@/services/api';

const router = useRouter();
const loading = ref(true);
const wishlist = ref([]);

async function fetchWishlist() {
    loading.value = true;
    try {
        const { data } = await wishlistApi.get();
        wishlist.value = data.data || [];
    } catch (error) {
        console.error('Error fetching wishlist:', error);
    } finally {
        loading.value = false;
    }
}

async function removeFromWishlist(courseId) {
    try {
        await wishlistApi.remove(courseId);
        wishlist.value = wishlist.value.filter(item => item.id !== courseId);
    } catch (error) {
        console.error('Error removing from wishlist:', error);
    }
}

async function addToCart(courseId) {
    try {
        await cartApi.add({ course_id: courseId });
        await removeFromWishlist(courseId);
        router.push('/student/cart');
    } catch (error) {
        console.error('Error adding to cart:', error);
    }
}

onMounted(() => {
    fetchWishlist();
});
</script>