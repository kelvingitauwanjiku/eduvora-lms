<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Schedule Live Class</h1>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-2xl">
            <form @submit.prevent="schedule" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input v-model="form.title" type="text" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                    <select v-model="form.course_id" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Select course</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input v-model="form.date" type="date" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <input v-model="form.time" type="time" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                    <input v-model="form.duration" type="number" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">Schedule</button>
                    <router-link to="/instructor/live-classes" class="px-6 py-2 border rounded-lg">Cancel</router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
const router = useRouter();
const form = reactive({ title: '', course_id: '', date: '', time: '', duration: 60, description: '' });
function schedule() { console.log('schedule', form); router.push('/instructor/live-classes'); }
</script>