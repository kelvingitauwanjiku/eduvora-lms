<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-blue-100 rounded-full mb-8">
                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-6xl font-bold text-gray-900">404</h1>
            <p class="mt-4 text-xl text-gray-600">Page not found</p>
            <p class="mt-2 text-gray-500">The page you're looking for doesn't exist or has been moved.</p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <router-link to="/" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Go Home
                </router-link>
                <button @click="$router.back()" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Go Back
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>