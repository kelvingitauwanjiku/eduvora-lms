<template>
  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Checkout</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Checkout Form -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Payment Method -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>
          
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <button
              v-for="gateway in gateways"
              :key="gateway.slug"
              @click="selectedGateway = gateway.slug"
              :class="[
                'flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-all',
                selectedGateway === gateway.slug 
                  ? 'border-blue-500 bg-blue-50' 
                  : 'border-gray-200 hover:border-gray-300'
              ]"
            >
              <div class="w-12 h-12 mb-2 flex items-center justify-center">
                <component :is="getGatewayIcon(gateway.slug)" class="w-10 h-10" />
              </div>
              <span class="text-sm font-medium text-gray-700">{{ gateway.name }}</span>
            </button>
          </div>
        </div>
        
        <!-- Card Details (for Stripe) -->
        <div v-if="selectedGateway === 'stripe'" class="bg-white rounded-2xl border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Card Details</h2>
          
          <form @submit.prevent="processPayment" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
              <input
                v-model="card.number"
                type="text"
                placeholder="1234 5678 9012 3456"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry</label>
                <input
                  v-model="card.expiry"
                  type="text"
                  placeholder="MM/YY"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                <input
                  v-model="card.cvc"
                  type="text"
                  placeholder="123"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
          </form>
        </div>
        
        <!-- Billing Info -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
              <input
                v-model="billing.name"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                v-model="billing.email"
                type="email"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
              <input
                v-model="billing.address"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
              <input
                v-model="billing.city"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
              <input
                v-model="billing.postalCode"
                type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
        </div>
      </div>
      
      <!-- Order Summary -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-4">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
          
          <!-- Items -->
          <div class="space-y-4 mb-6">
            <div v-for="item in cartItems" :key="item.id" class="flex gap-4">
              <img :src="item.thumbnail" class="w-16 h-12 object-cover rounded-lg" />
              <div class="flex-1">
                <h3 class="font-medium text-gray-900 text-sm">{{ item.title }}</h3>
                <p class="text-sm text-gray-500">{{ item.instructor }}</p>
              </div>
              <span class="font-semibold text-gray-900">{{ formatPrice(item.price) }}</span>
            </div>
          </div>
          
          <!-- Coupon -->
          <div class="flex gap-2 mb-6">
            <input
              v-model="couponCode"
              type="text"
              placeholder="Coupon code"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-sm"
            />
            <button @click="applyCoupon" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200">
              Apply
            </button>
          </div>
          
          <!-- Totals -->
          <div class="space-y-3 border-t border-gray-200 pt-4">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Subtotal</span>
              <span class="text-gray-900">{{ formatPrice(subtotal) }}</span>
            </div>
            <div v-if="discount > 0" class="flex justify-between text-sm">
              <span class="text-green-600">Discount</span>
              <span class="text-green-600">-{{ formatPrice(discount) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Tax</span>
              <span class="text-gray-900">{{ formatPrice(tax) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
              <span>Total</span>
              <span>{{ formatPrice(total) }}</span>
            </div>
          </div>
          
          <!-- Pay Button -->
          <button
            @click="processPayment"
            :disabled="processing"
            class="w-full mt-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="processing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ processing ? 'Processing...' : `Pay ${formatPrice(total)}` }}
          </button>
          
          <p class="text-xs text-gray-500 text-center mt-4">
            Secure payment powered by stripe
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../../stores/auth';
import { paymentApi } from '../../../services/api';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const gateways = ref([]);
const selectedGateway = ref('stripe');
const processing = ref(false);

const cartItems = ref([
  { id: 1, title: 'Complete Web Development Bootcamp', instructor: 'John Smith', price: 99.99, thumbnail: 'https://via.placeholder.com/100x60' }
]);

const couponCode = ref('');
const discount = ref(0);

const card = ref({
  number: '',
  expiry: '',
  cvc: ''
});

const billing = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  address: '',
  city: '',
  postalCode: ''
});

const subtotal = computed(() => cartItems.value.reduce((sum, item) => sum + item.price, 0));
const tax = computed(() => subtotal.value * 0.1);
const total = computed(() => subtotal.value + tax.value - discount.value);

function formatPrice(price) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
}

function getGatewayIcon(slug) {
  const icons = {
    stripe: 'IconCreditCard',
    paypal: 'IconBrandPaypal',
    razorpay: 'IconCash',
    paystack: 'IconBuildingBank'
  };
  return icons[slug] || 'IconCreditCard';
}

async function applyCoupon() {
  try {
    const { data } = await paymentApi.applyCoupon({ code: couponCode.value });
    discount.value = data.discount;
  } catch (error) {
    console.error('Coupon error:', error);
  }
}

async function processPayment() {
  processing.value = true;
  try {
    const { data } = await paymentApi.initiate(selectedGateway.value, {
      items: cartItems.value,
      gateway: selectedGateway.value,
      billing: billing.value,
      coupon: couponCode.value
    });
    
    if (data.redirect_url) {
      window.location.href = data.redirect_url;
    } else {
      router.push({ name: 'purchases', query: { success: true } });
    }
  } catch (error) {
    console.error('Payment error:', error);
  } finally {
    processing.value = false;
  }
}
</script>