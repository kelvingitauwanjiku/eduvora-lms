<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Templates</h1>
                <p class="mt-1 text-gray-500">Manage automated email templates</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="template in templates" :key="template.id" class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">{{ template.name }}</h3>
                    <button class="text-blue-600 hover:text-blue-700 text-sm">Edit</button>
                </div>
                <p class="text-sm text-gray-500">{{ template.description }}</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs px-2 py-1 bg-gray-100 rounded">{{ template.subject }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
const templates = ref([
    { id: 1, name: 'Welcome Email', description: 'Sent when a user registers', subject: 'Welcome to Eduvora' },
    { id: 2, name: 'Course Enrollment', description: 'Sent when a student enrolls', subject: 'You are enrolled!' },
    { id: 3, name: 'Certificate Issued', description: 'Sent when certificate is generated', subject: 'Congratulations!' },
    { id: 4, name: 'Password Reset', description: 'Password reset link', subject: 'Reset your password' },
]);
</script>
