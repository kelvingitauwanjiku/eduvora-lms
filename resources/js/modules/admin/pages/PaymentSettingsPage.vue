<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Payment Settings</h1>
            <p class="mt-1 text-gray-500">Configure payment gateways</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Stripe</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="stripe.enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="space-y-4" v-if="stripe.enabled">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        <input v-model="stripe.api_key" type="password" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                        <input v-model="stripe.secret_key" type="password" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">PayPal</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="paypal.enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="space-y-4" v-if="paypal.enabled">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                        <input v-model="paypal.client_id" type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret</label>
                        <input v-model="paypal.secret" type="password" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Razorpay</h2>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="razorpay.enabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="space-y-4" v-if="razorpay.enabled">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Key ID</label>
                        <input v-model="razorpay.key_id" type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Key Secret</label>
                        <input v-model="razorpay.key_secret" type="password" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Currency</h2>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Currency</label>
                    <select v-model="currency" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="GBP">GBP (£)</option>
                        <option value="INR">INR (₹)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button @click="saveSettings" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Settings</button>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const stripe = reactive({ enabled: true, api_key: '', secret_key: '' });
const paypal = reactive({ enabled: false, client_id: '', secret: '' });
const razorpay = reactive({ enabled: false, key_id: '', key_secret: '' });
const currency = ref('USD');

function saveSettings() {
    alert('Payment settings saved!');
}
</script>