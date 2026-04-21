<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <router-link to="/instructor/quizzes" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </router-link>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quiz Results</h1>
                    <p class="mt-1 text-gray-500">{{ quiz.title }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button @click="exportResults" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <div v-if="loading" class="animate-pulse">
            <div class="bg-gray-200 h-48 rounded-xl mb-6"></div>
            <div class="bg-gray-200 h-96 rounded-xl"></div>
        </div>

        <template v-else>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">Total Attempts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.total_attempts }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">Average Score</p>
                    <p class="text-2xl font-bold text-gray-900">{{ stats.avg_score }}%</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">Pass Rate</p>
                    <p class="text-2xl font-bold text-green-600">{{ stats.pass_rate }}%</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <p class="text-sm text-gray-500">Avg Time</p>
                    <p class="text-2xl font-bold text-gray-900">{{ formatTime(stats.avg_time) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 mb-6">
                <div class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input v-model="searchQuery" type="text" placeholder="Search students..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <select v-model="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="">All Results</option>
                        <option value="passed">Passed</option>
                        <option value="failed">Failed</option>
                    </select>
                    <select v-model="sortBy" class="px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest">Highest Score</option>
                        <option value="lowest">Lowest Score</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div v-if="filteredResults.length === 0" class="text-center py-12 text-gray-500">
                    No results found
                </div>
                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correct/Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="result in filteredResults" :key="result.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="result.user?.avatar || '/images/avatar-placeholder.jpg'" class="w-10 h-10 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ result.user?.name }}</p>
                                        <p class="text-sm text-gray-500">{{ result.user?.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-lg font-bold" :class="result.passed ? 'text-green-600' : 'text-red-600'">
                                    {{ result.score }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ result.correct_answers }}/{{ result.total_questions }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ formatTime(result.time_spent) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                    :class="result.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ result.passed ? 'Passed' : 'Failed' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ formatDate(result.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <button @click="viewDetails(result)" class="text-sm text-blue-600 hover:text-blue-700">
                                    Review
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="selectedResult" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="selectedResult = null">
                <div class="bg-white rounded-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Review Answers</h3>
                            <p class="text-sm text-gray-500">{{ selectedResult.user?.name }} - {{ formatDate(selectedResult.created_at) }}</p>
                        </div>
                        <button @click="selectedResult = null" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-6">
                        <div v-for="(answer, index) in selectedResult.answers" :key="index" class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start gap-3 mb-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                    :class="answer.is_correct ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                    <svg v-if="answer.is_correct" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ answer.question }}</p>
                                    <p class="text-sm text-gray-500 mt-1">Your answer: {{ answer.user_answer }}</p>
                                    <p v-if="!answer.is_correct" class="text-sm text-green-600 mt-1">Correct: {{ answer.correct_answer }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { quizApi } from '@/services/api';

const route = useRoute();

const loading = ref(true);
const quiz = ref({ title: '', id: null });
const results = ref([]);
const searchQuery = ref('');
const statusFilter = ref('');
const sortBy = ref('newest');
const selectedResult = ref(null);

const stats = computed(() => {
    const total = results.value.length;
    if (total === 0) return { total_attempts: 0, avg_score: 0, pass_rate: 0, avg_time: 0 };
    
    const passed = results.value.filter(r => r.passed).length;
    const avgScore = results.value.reduce((sum, r) => sum + r.score, 0) / total;
    const avgTime = results.value.reduce((sum, r) => sum + (r.time_spent || 0), 0) / total;
    
    return {
        total_attempts: total,
        avg_score: Math.round(avgScore),
        pass_rate: Math.round((passed / total) * 100),
        avg_time: avgTime,
    };
});

const filteredResults = computed(() => {
    let result = [...results.value];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(r => 
            r.user?.name?.toLowerCase().includes(query) ||
            r.user?.email?.toLowerCase().includes(query)
        );
    }
    
    if (statusFilter.value) {
        result = result.filter(r => statusFilter.value === 'passed' ? r.passed : !r.passed);
    }
    
    switch (sortBy.value) {
        case 'oldest':
            result.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            break;
        case 'highest':
            result.sort((a, b) => b.score - a.score);
            break;
        case 'lowest':
            result.sort((a, b) => a.score - b.score);
            break;
        default:
            result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
    
    return result;
});

function formatTime(seconds) {
    if (!seconds) return '-';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}m ${secs}s`;
}

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function viewDetails(result) {
    selectedResult.value = result;
}

function exportResults() {
    const csv = [
        ['Student', 'Email', 'Score', 'Status', 'Time', 'Date'].join(','),
        ...filteredResults.value.map(r => [
            r.user?.name,
            r.user?.email,
            `${r.score}%`,
            r.passed ? 'Passed' : 'Failed',
            formatTime(r.time_spent),
            formatDate(r.created_at),
        ].join(','))
    ].join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `quiz-results-${quiz.value.id}.csv`;
    a.click();
}

async function fetchResults() {
    loading.value = true;
    try {
        const { data } = await quizApi.getResults(route.params.id);
        quiz.value = data.quiz || { title: '', id: route.params.id };
        results.value = data.results || [];
    } catch (error) {
        console.error('Error fetching results:', error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchResults();
});
</script>