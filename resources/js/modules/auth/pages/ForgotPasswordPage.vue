<template>
    <div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <router-link to="/" class="inline-flex items-center gap-2">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <span class="text-white font-bold text-2xl">E</span>
                    </div>
                </router-link>
                <h1 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Reset your password</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Enter your email and we'll send you reset instructions</p>
            </div>

            <!-- Reset Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" v-model="form.email" type="email" required
                                   :disabled="sending || sent"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                   placeholder="you@example.com">
                        </div>
                        <p v-if="errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.email }}</p>
                    </div>

                    <!-- Success State -->
                    <div v-if="sent" class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">Check your email</p>
                                <p class="text-sm text-green-700 dark:text-green-300 mt-0.5">We've sent password reset instructions to {{ form.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="apiError" class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-red-600 dark:text-red-400">{{ apiError }}</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="sending || sent"
                            class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg hover:shadow-blue-500/25 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                        <svg v-if="sending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-if="!sent">{{ sending ? 'Sending...' : 'Send reset link' }}</span>
                        <span v-else>Email sent!</span>
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <router-link to="/login" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to login
                    </router-link>
                </div>
            </div>

            <!-- Footer Note -->
            <p class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
                Having trouble? Contact our <router-link to="/support" class="text-blue-600 hover:text-blue-700 dark:text-blue-400">support team</router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useAuthStore } from '../../../stores/auth';

const authStore = useAuthStore();

const form = reactive({
    email: ''
});

const sending = ref(false);
const sent = ref(false);
const apiError = ref('');
const errors = reactive({
    email: ''
});

function validateForm() {
    let isValid = true;
    errors.email = '';

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!form.email || !emailRegex.test(form.email)) {
        errors.email = 'Please enter a valid email address';
        isValid = false;
    }

    return isValid;
}

async function handleSubmit() {
    if (!validateForm()) return;

    sending.value = true;
    apiError.value = '';

    try {
        await authStore.forgotPassword(form.email);
        sent.value = true;
    } catch (err) {
        apiError.value = err.response?.data?.message || 'Failed to send reset link. Please try again.';
    } finally {
        sending.value = false;
    }
}
</script>
