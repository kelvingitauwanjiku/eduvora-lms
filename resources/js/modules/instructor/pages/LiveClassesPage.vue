<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Live Classes</h1>
                <p class="mt-1 text-gray-500">Schedule and manage live sessions</p>
            </div>
            <router-link to="/instructor/live-classes/create" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Schedule New
            </router-link>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <select v-model="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Status</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="live">Live Now</option>
                    <option value="completed">Completed</option>
                </select>
                <select v-model="courseFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Courses</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="i in 6" :key="i" class="animate-pulse bg-white rounded-xl border border-gray-200 p-6">
                <div class="h-5 bg-gray-200 rounded w-3/4 mb-4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>

        <div v-else-if="filteredClasses.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No live classes</h3>
            <p class="text-gray-500 mt-1">Schedule your first live class</p>
            <router-link to="/instructor/live-classes/create" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Schedule Live Class
            </router-link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="liveClass in filteredClasses" :key="liveClass.id" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                        :class="{
                            'bg-blue-100 text-blue-800': liveClass.status === 'scheduled',
                            'bg-green-100 text-green-800': liveClass.status === 'live',
                            'bg-gray-100 text-gray-800': liveClass.status === 'completed',
                        }">
                        {{ liveClass.status || 'scheduled' }}
                    </span>
                    <button @click="toggleMenu(liveClass.id)" class="p-1 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                    </button>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ liveClass.title }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ liveClass.course?.title }}</p>
                <div class="space-y-2 text-sm text-gray-500 mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ formatDate(liveClass.scheduled_at) }}
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ liveClass.duration }} minutes
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ liveClass.attendees || 0 }} attendees
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100 flex gap-2">
                    <button v-if="liveClass.status === 'scheduled'" @click="startClass(liveClass)" 
                        class="flex-1 px-3 py-2 text-sm text-center bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Start Class
                    </button>
                    <button v-if="liveClass.status === 'live'" @click="joinClass(liveClass)" 
                        class="flex-1 px-3 py-2 text-sm text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Join Class
                    </button>
                    <button v-if="liveClass.status === 'completed'" @click="viewRecording(liveClass)" 
                        class="flex-1 px-3 py-2 text-sm text-center border border-gray-200 rounded-lg hover:bg-gray-50">
                        View Recording
                    </button>
                    <button @click="editClass(liveClass)" 
                        class="flex-1 px-3 py-2 text-sm text-center border border-gray-200 rounded-lg hover:bg-gray-50">
                        Edit
                    </button>
                </div>
                <div v-if="liveClass.status === 'live'" class="mt-2 flex items-center justify-center gap-2">
                    <span class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
                    <span class="text-xs text-red-600 font-medium">LIVE NOW</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { liveClassApi, courseApi } from '@/services/api';

const router = useRouter();

const loading = ref(true);
const liveClasses = ref([]);
const courses = ref([]);
const statusFilter = ref('');
const courseFilter = ref('');
const activeMenu = ref(null);

const filteredClasses = computed(() => {
    let result = [...liveClasses.value];
    
    if (statusFilter.value) {
        result = result.filter(c => c.status === statusFilter.value);
    }
    
    if (courseFilter.value) {
        result = result.filter(c => c.course_id === Number(courseFilter.value));
    }
    
    return result.sort((a, b) => new Date(b.scheduled_at) - new Date(a.scheduled_at));
});

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleString('en-US', { 
        month: 'short', day: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    });
}

function toggleMenu(id) {
    activeMenu.value = activeMenu.value === id ? null : id;
}

function startClass(liveClass) {
    window.open(`/instructor/live-classes/${liveClass.id}/room`, '_blank');
}

function joinClass(liveClass) {
    window.open(`/instructor/live-classes/${liveClass.id}/room`, '_blank');
}

function viewRecording(liveClass) {
    window.open(liveClass.recording_url, '_blank');
}

function editClass(liveClass) {
    router.push(`/instructor/live-classes/${liveClass.id}/edit`);
}

async function fetchLiveClasses() {
    loading.value = true;
    try {
        const { data } = await liveClassApi.getAll({ per_page: 100 });
        liveClasses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching live classes:', error);
    } finally {
        loading.value = false;
    }
}

async function fetchCourses() {
    try {
        const { data } = await courseApi.getAll({ per_page: 100 });
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
    }
}

onMounted(() => {
    fetchLiveClasses();
    fetchCourses();
});
</script>