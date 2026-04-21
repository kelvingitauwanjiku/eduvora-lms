<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-500 mt-2">Complete your purchase</p>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <div v-else-if="items.length === 0" class="text-center py-12 bg-white rounded-xl border border-gray-200">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg">Your cart is empty</p>
            <router-link to="/courses" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Browse Courses
            </router-link>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Payment Method</h2>
                    <div class="space-y-3">
                        <label v-for="gateway in gateways" :key="gateway.id" 
                            class="flex items-center gap-4 p-4 border rounded-lg cursor-pointer transition-colors"
                            :class="selectedGateway === gateway.id ? 'border-blue-500 bg-blue-50' : 'hover:border-gray-300'">
                            <input type="radio" :value="gateway.id" v-model="selectedGateway" class="w-4 h-4 text-blue-600">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center">
                                    <component :is="getGatewayIcon(gateway.id)" class="w-8 h-5 text-gray-600" />
                                </div>
                                <span class="font-medium">{{ gateway.name }}</span>
                            </div>
                            <span v-if="gateway.fees" class="ml-auto text-sm text-gray-500">{{ gateway.fees }}</span>
                        </label>
                    </div>
                </div>

                <div v-if="selectedGateway === 'stripe'" class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Card Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                            <input v-model="card.number" type="text" placeholder="1234 5678 9012 3456" 
                                maxlength="19" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                @input="formatCardNumber">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input v-model="card.expiry" type="text" placeholder="MM/YY" maxlength="5"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    @input="formatExpiry">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                                <input v-model="card.cvc" type="text" placeholder="123" maxlength="4"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name on Card</label>
                            <input v-model="card.name" type="text" placeholder="John Doe"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div v-if="selectedGateway === 'paypal'" class="bg-white rounded-xl border border-gray-200 p-6 text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/>
                    </svg>
                    <p class="text-gray-500 mb-4">You will be redirected to PayPal to complete payment</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold mb-4">Billing Address</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input v-model="billing.name" type="text" placeholder="John Doe" 
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="billing.email" type="email" placeholder="john@example.com" 
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input v-model="billing.address" type="text" placeholder="123 Main St" 
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input v-model="billing.city" type="text" placeholder="New York" 
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                            <input v-model="billing.zip" type="text" placeholder="10001" 
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <select v-model="billing.country" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Country</option>
                                <option v-for="country in countries" :key="country.code" :value="country.code">{{ country.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                    <div v-for="item in items" :key="item.id" class="flex gap-3 py-3 border-b">
                        <img :src="item.thumbnail || '/images/course-placeholder.jpg'" class="w-16 h-12 rounded object-cover">
                        <div class="flex-1">
                            <p class="font-medium text-sm line-clamp-2">{{ item.title }}</p>
                            <p class="text-gray-500 text-sm">${{ item.price }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div v-if="!couponApplied" class="flex gap-2">
                            <input v-model="couponCode" type="text" placeholder="Coupon code" 
                                class="flex-1 px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <button @click="applyCoupon" :disabled="!couponCode || applyingCoupon"
                                class="px-3 py-2 bg-gray-900 text-white rounded-lg text-sm disabled:opacity-50">
                                {{ applyingCoupon ? '...' : 'Apply' }}
                            </button>
                        </div>
                        <div v-else class="flex items-center justify-between p-2 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-green-700">{{ couponCode }}</span>
                            </div>
                            <button @click="removeCoupon" class="text-xs text-red-600">Remove</button>
                        </div>
                        <p v-if="couponError" class="mt-2 text-sm text-red-600">{{ couponError }}</p>
                    </div>

                    <div class="py-3 border-b space-y-2 mt-4">
                        <div class="flex justify-between text-sm"><span>Subtotal</span><span>${{ subtotal }}</span></div>
                        <div v-if="discount > 0" class="flex justify-between text-sm text-green-600"><span>Discount</span><span>-${{ discount }}</span></div>
                        <div v-if="processingFee > 0" class="flex justify-between text-sm text-gray-500"><span>Processing Fee</span><span>${{ processingFee }}</span></div>
                    </div>
                    <div class="py-3 flex justify-between font-semibold text-lg">
                        <span>Total</span><span>${{ total }}</span>
                    </div>
                    <button @click="processPayment" :disabled="processing || !selectedGateway"
                        class="w-full mt-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 flex items-center justify-center gap-2">
                        <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ processing ? 'Processing...' : `Pay $${total}` }}
                    </button>
                    <p class="text-xs text-gray-500 mt-3 text-center">Secure payment powered by {{ getGatewayName(selectedGateway) }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, h } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { cartApi, paymentApi } from '@/services/api';

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const processing = ref(false);
const items = ref([]);
const gateways = ref([]);
const selectedGateway = ref('');
const couponCode = ref('');
const applyingCoupon = ref(false);
const couponApplied = ref(false);
const couponError = ref('');
const discount = ref(0);
const processingFee = ref(0);

const card = ref({ number: '', expiry: '', cvc: '', name: '' });
const billing = ref({ name: '', email: '', address: '', city: '', zip: '', country: '' });

const countries = ref([
    { code: 'US', name: 'United States' },
    { code: 'GB', name: 'United Kingdom' },
    { code: 'CA', name: 'Canada' },
    { code: 'AU', name: 'Australia' },
    { code: 'IN', name: 'India' },
    { code: 'DE', name: 'Germany' },
    { code: 'FR', name: 'France' },
]);

const CreditCardIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })
        ]);
    }
};

const PayPalIcon = {
    render() {
        return h('svg', { fill: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { d: 'M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z' })
        ]);
    }
};

function getGatewayIcon(id) {
    const icons = { stripe: CreditCardIcon, paypal: PayPalIcon, razorpay: CreditCardIcon, flutterwave: CreditCardIcon };
    return icons[id] || CreditCardIcon;
}

function getGatewayName(id) {
    const gateway = gateways.value.find(g => g.id === id);
    return gateway ? gateway.name : id;
}

function formatCardNumber(e) {
    let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
    value = value.match(/.{1,4}/g)?.join(' ') || value;
    card.value.number = value;
}

function formatExpiry(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    card.value.expiry = value;
}

const subtotal = computed(() => items.value.reduce((sum, i) => sum + (i.price || 0), 0));
const total = computed(() => subtotal.value - discount.value + processingFee.value);

async function loadCart() {
    try {
        loading.value = true;
        const [cartRes, gatewaysRes] = await Promise.all([
            cartApi.get(),
            paymentApi.getGateways()
        ]);
        items.value = cartRes.data.cart || cartRes.data || [];
        gateways.value = gatewaysRes.data || [];
        if (gateways.value.length > 0) {
            selectedGateway.value = gateways.value[0].id;
        }
        if (authStore.user) {
            billing.value.name = authStore.user.name || '';
            billing.value.email = authStore.user.email || '';
        }
    } catch (error) {
        console.error('Failed to load cart:', error);
    } finally {
        loading.value = false;
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
    couponError.value = '';
}

async function processPayment() {
    processing.value = true;
    try {
        if (selectedGateway.value === 'stripe') {
            const response = await paymentApi.initiate('stripe', {
                items: items.value,
                billing: billing.value,
                card: card.value,
                coupon: couponCode.value
            });
            if (response.data.client_secret) {
                alert('Payment initiated successfully!');
            }
        } else if (selectedGateway.value === 'paypal') {
            const response = await paymentApi.initiate('paypal', {
                items: items.value,
                billing: billing.value,
                coupon: couponCode.value
            });
            if (response.data.approval_url) {
                window.location.href = response.data.approval_url;
                return;
            }
        } else {
            const response = await paymentApi.initiate(selectedGateway.value, {
                items: items.value,
                billing: billing.value,
                coupon: couponCode.value
            });
            if (response.data.checkout_url) {
                window.location.href = response.data.checkout_url;
                return;
            }
        }
        router.push('/student/purchases');
    } catch (error) {
        alert(error.response?.data?.message || 'Payment failed. Please try again.');
    } finally {
        processing.value = false;
    }
}

onMounted(() => {
    loadCart();
});
</script>