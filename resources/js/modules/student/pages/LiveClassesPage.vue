<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Live Classes</h1>
            <p class="mt-1 text-gray-500">Join live sessions with instructors</p>
        </div>

        <div v-if="loading" class="space-y-4">
            <div v-for="i in 3" :key="i" class="animate-pulse bg-white rounded-xl border border-gray-200 p-6">
                <div class="h-6 bg-gray-200 rounded w-1/3 mb-3"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>

        <div class="space-y-8">
            <div v-if="upcomingClasses.length > 0">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Upcoming Classes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="live in upcomingClasses" :key="live.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition">
                        <div class="aspect-video bg-gradient-to-br from-purple-100 to-blue-100 relative">
                            <img v-if="live.thumbnail" :src="live.thumbnail" class="w-full h-full object-cover">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 bg-white/50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div v-if="isLiveNow(live.scheduled_at)" class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-medium rounded">
                                LIVE
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 line-clamp-2">{{ live.title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ live.instructor?.name }}</p>
                            <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(live.scheduled_at) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ live.duration || 60 }} min
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="text-xs text-gray-500">{{ live.total_enrolled || 0 }} enrolled</span>
                                <span v-if="live.price === 0" class="text-xs text-green-600 font-medium">Free</span>
                                <span v-else class="text-xs text-gray-500">${{ live.price }}</span>
                            </div>
                            <button v-if="isLiveNow(live.scheduled_at)" @click="joinClass(live)" class="w-full mt-4 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium">
                                Join Now
                            </button>
                            <button v-else-if="canJoin(live)" @click="joinClass(live)" class="w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                Join Class
                            </button>
                            <button v-else @click="enrollClass(live)" class="w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                Enroll
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="pastRecordings.length > 0">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Past Recordings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="recording in pastRecordings" :key="recording.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition">
                        <div class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200 relative">
                            <img v-if="recording.thumbnail" :src="recording.thumbnail" class="w-full h-full object-cover">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 bg-white/50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 line-clamp-2">{{ recording.title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ recording.instructor?.name }}</p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-xs text-gray-500">{{ formatDate(recording.scheduled_at) }}</span>
                                <span class="text-xs text-gray-500">{{ recording.duration || '~60' }} min</span>
                            </div>
                            <button @click="watchRecording(recording)" class="w-full mt-4 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                Watch Recording
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="upcomingClasses.length === 0 && pastRecordings.length === 0 && !loading" class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No live classes available</h3>
                <p class="text-gray-500 mt-1">Check back later for upcoming sessions</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const loading = ref(true);
const upcomingClasses = ref([]);
const pastRecordings = ref([]);

async function fetchLiveClasses() {
    try {
        loading.value = true;
        const { data } = await api.get('/live-classes/my-classes');
        upcomingClasses.value = data.upcoming || [];
        pastRecordings.value = data.recordings || [];
    } catch (error) {
        console.error('Error fetching live classes:', error);
    } finally {
        loading.value = false;
    }
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleString('en-US', { 
        month: 'short', 
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}

function isLiveNow(date) {
    if (!date) return false;
    const now = new Date();
    const scheduled = new Date(date);
    const diff = Math.abs(now - scheduled);
    return diff < 15 * 60 * 1000;
}

function canJoin(live) {
    return live.enrolled && !isLiveNow(live.scheduled_at);
}

function joinClass(live) {
    if (live.meeting_url) {
        window.open(live.meeting_url, '_blank');
    }
}

function watchRecording(recording) {
    if (recording.video_url) {
        window.open(recording.video_url, '_blank');
    }
}

async function enrollClass(live) {
    try {
        await api.post(`/live-classes/${live.id}/enroll`);
        await fetchLiveClasses();
    } catch (error) {
        console.error('Error enrolling in class:', error);
    }
}

onMounted(() => {
    fetchLiveClasses();
});
</script>