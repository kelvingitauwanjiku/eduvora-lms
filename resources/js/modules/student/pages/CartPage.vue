<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
            <p class="mt-1 text-gray-500">{{ cartItems.length }} courses in your cart</p>
        </div>

        <div v-if="loading" class="space-y-4">
            <div v-for="i in 3" :key="i" class="animate-pulse bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex gap-6">
                    <div class="w-48 h-28 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1">
                        <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="cartItems.length === 0" class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.427 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Your cart is empty</h3>
            <p class="text-gray-500 mt-1">Browse our courses and start learning</p>
            <router-link to="/courses" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Browse Courses
            </router-link>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                <div v-for="item in cartItems" :key="item.id" class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex gap-6">
                        <router-link :to="`/courses/${item.id}`" class="shrink-0">
                            <img :src="item.thumbnail || '/images/course-placeholder.jpg'" class="w-48 h-28 rounded-lg object-cover">
                        </router-link>
                        <div class="flex-1">
                            <router-link :to="`/courses/${item.id}`" class="font-semibold text-gray-900 hover:text-blue-600">
                                {{ item.title }}
                            </router-link>
                            <p class="text-sm text-gray-500 mt-1">{{ item.instructor?.name }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.95 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.95 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-sm">{{ item.rating || '0.0' }}</span>
                                </div>
                                <span class="text-gray-300">|</span>
                                <span class="text-sm text-gray-500">{{ item.total_enrolled || 0 }} students</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div v-if="item.is_free" class="text-xl font-bold text-green-600">Free</div>
                            <div v-else class="text-xl font-bold text-gray-900">${{ item.price }}</div>
                            <button @click="removeFromCart(item.id)" class="text-sm text-red-600 hover:text-red-700 mt-2">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 h-fit sticky top-24">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Summary</h2>
                
                <div class="mb-4">
                    <div class="flex gap-2">
                        <input v-model="couponCode" type="text" placeholder="Coupon code" 
                            class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button @click="applyCoupon" :disabled="!couponCode || applyingCoupon"
                            class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800 disabled:opacity-50">
                            {{ applyingCoupon ? '...' : 'Apply' }}
                        </button>
                    </div>
                    <p v-if="couponError" class="mt-2 text-sm text-red-600">{{ couponError }}</p>
                    <div v-if="couponApplied" class="mt-2 flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded-lg">
                        <span class="text-sm text-green-700">{{ couponCode }} applied!</span>
                        <button @click="removeCoupon" class="text-sm text-red-600">Remove</button>
                    </div>
                </div>

                <div class="space-y-3 text-sm border-t pt-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal ({{ cartItems.length }} courses)</span>
                        <span class="font-medium">${{ subtotal }}</span>
                    </div>
                    <div v-if="discount > 0" class="flex justify-between">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-medium text-green-600">-${{ discount }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="font-bold text-gray-900">${{ total }}</span>
                    </div>
                </div>
                <button @click="checkout" :disabled="processing" class="w-full mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                    <span v-if="processing">Processing...</span>
                    <span v-else>Proceed to Checkout</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { cartApi } from '@/services/api';

const router = useRouter();

const loading = ref(true);
const processing = ref(false);
const applyingCoupon = ref(false);
const couponCode = ref('');
const couponApplied = ref(false);
const couponError = ref('');
const discount = ref(0);
const cartItems = ref([]);

const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (item.price || 0), 0);
});
const total = computed(() => subtotal.value - discount.value);

async function fetchCart() {
    loading.value = true;
    try {
        const { data } = await cartApi.get();
        cartItems.value = data.data || [];
    } catch (error) {
        console.error('Error fetching cart:', error);
    } finally {
        loading.value = false;
    }
}

async function removeFromCart(id) {
    try {
        await cartApi.remove(id);
        cartItems.value = cartItems.value.filter(item => item.id !== id);
    } catch (error) {
        console.error('Error removing from cart:', error);
    }
}

async function applyCoupon() {
    if (!couponCode.value) return;
    try {
        applyingCoupon.value = true;
        couponError.value = '';
        const response = await cartApi.applyCoupon({ code: couponCode.value });
        if (response.data.success) {
            discount.value = response.data.discount || 0;
            couponApplied.value = true;
        }
    } catch (error) {
        couponError.value = error.response?.data?.message || 'Invalid coupon code';
    } finally {
        applyingCoupon.value = false;
    }
}

function removeCoupon() {
    discount.value = 0;
    couponApplied.value = false;
    couponCode.value = '';
}

async function checkout() {
    router.push('/student/checkout');
}

onMounted(() => {
    fetchCart();
});
</script>