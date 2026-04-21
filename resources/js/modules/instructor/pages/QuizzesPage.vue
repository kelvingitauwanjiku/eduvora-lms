<template>
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Quizzes</h1>
                <p class="mt-1 text-gray-500">Manage course quizzes</p>
            </div>
            <router-link to="/instructor/quizzes/create" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Quiz
            </router-link>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 mb-6">
            <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="searchQuery" type="text" placeholder="Search quizzes..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <select v-model="courseFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Courses</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                </select>
                <select v-model="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
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

        <div v-else-if="filteredQuizzes.length === 0" class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No quizzes found</h3>
            <p class="text-gray-500 mt-1">{{ searchQuery ? 'Try adjusting your search' : 'Create your first quiz to test your students' }}</p>
            <router-link v-if="!searchQuery" to="/instructor/quizzes/create" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Quiz
            </router-link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="quiz in filteredQuizzes" :key="quiz.id" class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                            :class="quiz.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            {{ quiz.status || 'draft' }}
                        </span>
                    </div>
                    <div class="relative">
                        <button @click="toggleMenu(quiz.id)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                        <div v-if="activeMenu === quiz.id" class="absolute right-0 top-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-10 min-w-[140px]">
                            <router-link :to="`/instructor/quizzes/${quiz.id}/edit`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Edit Quiz
                            </router-link>
                            <router-link :to="`/instructor/quizzes/${quiz.id}/results`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                View Results
                            </router-link>
                            <button @click="duplicateQuiz(quiz)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Duplicate
                            </button>
                            <hr class="my-2 border-gray-100">
                            <button @click="deleteQuiz(quiz)" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                <h3 class="font-semibold text-gray-900 text-lg mb-1">{{ quiz.title }}</h3>
                <p class="text-sm text-gray-500 mb-4 line-clamp-1">{{ quiz.course?.title || 'No course assigned' }}</p>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-4 text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ quiz.questions_count || quiz.questions?.length || 0 }} questions
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ quiz.time_limit || 0 }}m
                        </span>
                    </div>
                    <span class="text-gray-500">{{ quiz.attempts_count || 0 }} attempts</span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                    <router-link :to="`/instructor/quizzes/${quiz.id}/edit`" 
                        class="flex-1 px-3 py-2 text-sm text-center border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        Edit
                    </router-link>
                    <router-link :to="`/instructor/quizzes/${quiz.id}/results`" 
                        class="flex-1 px-3 py-2 text-sm text-center border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        Results
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { quizApi, courseApi } from '@/services/api';

const router = useRouter();

const loading = ref(true);
const quizzes = ref([]);
const courses = ref([]);
const searchQuery = ref('');
const courseFilter = ref('');
const statusFilter = ref('');
const activeMenu = ref(null);

const filteredQuizzes = computed(() => {
    let result = [...quizzes.value];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(q => 
            q.title?.toLowerCase().includes(query) ||
            q.course?.title?.toLowerCase().includes(query)
        );
    }
    
    if (courseFilter.value) {
        result = result.filter(q => q.course_id === Number(courseFilter.value));
    }
    
    if (statusFilter.value) {
        result = result.filter(q => q.status === statusFilter.value);
    }
    
    return result;
});

async function fetchQuizzes() {
    loading.value = true;
    try {
        const { data } = await quizApi.getAll({ per_page: 100 });
        quizzes.value = data.data || [];
    } catch (error) {
        console.error('Error fetching quizzes:', error);
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

function toggleMenu(quizId) {
    activeMenu.value = activeMenu.value === quizId ? null : quizId;
}

async function duplicateQuiz(quiz) {
    activeMenu.value = null;
    try {
        const { data } = await quizApi.create({
            title: `${quiz.title} (Copy)`,
            description: quiz.description,
            course_id: quiz.course_id,
            time_limit: quiz.time_limit,
            passing_score: quiz.passing_score,
            questions: quiz.questions,
        });
        router.push(`/instructor/quizzes/${data.id}/edit`);
    } catch (error) {
        console.error('Error duplicating quiz:', error);
    }
}

async function deleteQuiz(quiz) {
    activeMenu.value = null;
    if (confirm(`Are you sure you want to delete "${quiz.title}"?`)) {
        try {
            await quizApi.delete(quiz.id);
            quizzes.value = quizzes.value.filter(q => q.id !== quiz.id);
        } catch (error) {
            console.error('Error deleting quiz:', error);
        }
    }
}

function handleClickOutside(event) {
    if (!event.target.closest('.relative')) {
        activeMenu.value = null;
    }
}

onMounted(() => {
    fetchQuizzes();
    fetchCourses();
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
