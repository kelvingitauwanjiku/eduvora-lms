<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Live Classes</h1>
                <p class="mt-1 text-gray-500">Manage live class sessions</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Total Classes</p>
                <p class="text-2xl font-bold">{{ stats.total }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Upcoming</p>
                <p class="text-2xl font-bold">{{ stats.upcoming }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Live Now</p>
                <p class="text-2xl font-bold text-red-600">{{ stats.live }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="text-2xl font-bold">{{ stats.completed }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="live in classes" :key="live.id">
                        <td class="px-6 py-4 font-medium">{{ live.title }}</td>
                        <td class="px-6 py-4">{{ live.instructor }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(live.scheduled_at) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full" :class="{
                                'bg-green-100 text-green-800': live.status === 'completed',
                                'bg-yellow-100 text-yellow-800': live.status === 'scheduled',
                                'bg-red-100 text-red-800': live.status === 'live'
                            }">{{ live.status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-blue-600 hover:text-blue-700 text-sm">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { liveClassApi } from '@/services/api';

const stats = reactive({ total: 0, upcoming: 0, live: 0, completed: 0 });
const classes = ref([]);
const loading = ref(false);

async function fetchClasses() {
    loading.value = true;
    try {
        const { data } = await liveClassApi.getAll();
        classes.value = data.data || data;
        stats.total = classes.value.length;
        stats.upcoming = classes.value.filter(c => c.status === 'scheduled').length;
        stats.live = classes.value.filter(c => c.status === 'live').length;
        stats.completed = classes.value.filter(c => c.status === 'completed').length;
    } catch (err) {
        console.error('Failed to load classes:', err);
    } finally {
        loading.value = false;
    }
}

function formatDate(d) { return new Date(d).toLocaleString(); }

onMounted(() => { fetchClasses(); });
</script>