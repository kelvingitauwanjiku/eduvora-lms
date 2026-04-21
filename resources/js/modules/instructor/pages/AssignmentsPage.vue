<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Assignments</h1>
                <p class="mt-1 text-gray-500">Manage course assignments</p>
            </div>
            <button @click="showCreateModal = true" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Assignment
            </button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <select v-model="courseFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Courses</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                </select>
                <select v-model="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending">Pending Review</option>
                    <option value="graded">Graded</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="i in 6" :key="i" class="animate-pulse bg-white rounded-xl border border-gray-200 p-6">
                <div class="h-5 bg-gray-200 rounded w-3/4 mb-4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            </div>
        </div>

        <div v-else-if="assignments.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No assignments yet</h3>
            <p class="text-gray-500 mt-1">Create your first assignment</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="assignment in filteredAssignments" :key="assignment.id" 
                class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                        :class="{
                            'bg-yellow-100 text-yellow-800': assignment.status === 'pending',
                            'bg-green-100 text-green-800': assignment.status === 'graded',
                        }">
                        {{ assignment.status || 'pending' }}
                    </span>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ assignment.title }}</h3>
                <p class="text-sm text-gray-500 mb-4 line-clamp-1">{{ assignment.course?.title }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>{{ assignment.submissions_count || 0 }} submissions</span>
                    <span>Due: {{ formatDate(assignment.due_date) }}</span>
                </div>
                <div class="pt-4 border-t border-gray-100 flex gap-2">
                    <router-link :to="`/instructor/assignments/${assignment.id}/submissions`" 
                        class="flex-1 px-3 py-2 text-sm text-center border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        View Submissions
                    </router-link>
                    <button @click="editAssignment(assignment)" 
                        class="flex-1 px-3 py-2 text-sm text-center border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        Edit
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="showCreateModal = false">
            <div class="bg-white rounded-xl w-full max-w-lg">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Create Assignment</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="createAssignment" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input v-model="createForm.title" type="text" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <select v-model="createForm.course_id" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="">Select course</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea v-model="createForm.description" rows="3"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input v-model="createForm.due_date" type="datetime-local" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                        <input v-model="createForm.points" type="number" min="1" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateModal = false" 
                            class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { assignmentApi, courseApi } from '@/services/api';

const router = useRouter();

const loading = ref(true);
const assignments = ref([]);
const courses = ref([]);
const showCreateModal = ref(false);
const courseFilter = ref('');
const statusFilter = ref('');

const createForm = ref({
    title: '',
    course_id: '',
    description: '',
    due_date: '',
    points: 100,
});

const filteredAssignments = computed(() => {
    let result = [...assignments.value];
    
    if (courseFilter.value) {
        result = result.filter(a => a.course_id === Number(courseFilter.value));
    }
    
    if (statusFilter.value) {
        result = result.filter(a => a.status === statusFilter.value);
    }
    
    return result;
});

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function editAssignment(assignment) {
    router.push(`/instructor/assignments/${assignment.id}/edit`);
}

async function fetchAssignments() {
    loading.value = true;
    try {
        const { data } = await assignmentApi.getAll({ per_page: 100 });
        assignments.value = data.data || [];
    } catch (error) {
        console.error('Error fetching assignments:', error);
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

async function createAssignment() {
    try {
        await assignmentApi.create(createForm.value);
        showCreateModal.value = false;
        fetchAssignments();
    } catch (error) {
        console.error('Error creating assignment:', error);
    }
}

onMounted(() => {
    fetchAssignments();
    fetchCourses();
});
</script>