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
                    <h1 class="text-3xl font-bold text-gray-900">{{ isEditing ? 'Edit Quiz' : 'Create Quiz' }}</h1>
                    <p class="mt-1 text-gray-500">Build quizzes with multiple question types</p>
                </div>
            </div>
            <button @click="saveQuiz" :disabled="saving" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
                <svg v-if="saving" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ saving ? 'Saving...' : 'Save Quiz' }}
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Quiz Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title *</label>
                            <input v-model="quiz.title" type="text" required placeholder="e.g., Chapter 1 Quiz"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="quiz.description" rows="3" placeholder="Instructions for students..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Questions ({{ quiz.questions.length }})</h2>
                        <div class="flex gap-2">
                            <button type="button" @click="addQuestion('mcq')" class="px-3 py-1 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                MCQ
                            </button>
                            <button type="button" @click="addQuestion('true_false')" class="px-3 py-1 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                True/False
                            </button>
                            <button type="button" @click="addQuestion('fill_blank')" class="px-3 py-1 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Fill Blank
                            </button>
                        </div>
                    </div>
                    
                    <div v-if="quiz.questions.length === 0" class="text-center py-8 text-gray-500">
                        <p>No questions yet. Add questions using the buttons above.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="(question, index) in quiz.questions" :key="index" class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium px-2 py-1 bg-gray-100 rounded">{{ question.type.toUpperCase() }}</span>
                                    <span class="text-sm text-gray-500">Q{{ index + 1 }}</span>
                                </div>
                                <button type="button" @click="removeQuestion(index)" class="text-red-600 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="mb-4">
                                <input v-model="question.question" type="text" placeholder="Enter question" 
                                    class="w-full font-medium border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div v-if="question.type === 'mcq'" class="space-y-2">
                                <div v-for="(option, oi) in question.options" :key="oi" class="flex items-center gap-2">
                                    <input type="radio" :name="`q${index}-correct`" :checked="option === question.correct" 
                                        @change="question.correct = option" class="w-4 h-4 text-blue-600">
                                    <input v-model="question.options[oi]" type="text" :placeholder="`Option ${oi + 1}`" 
                                        class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <button type="button" @click="removeOption(index, oi)" v-if="oi > 1" class="text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <button type="button" @click="addOption(index)" v-if="question.options.length < 6" 
                                    class="text-sm text-blue-600 hover:text-blue-700">
                                    + Add Option
                                </button>
                            </div>
                            
                            <div v-if="question.type === 'true_false'" class="mt-3 flex gap-6">
                                <label class="flex items-center gap-2">
                                    <input type="radio" v-model="question.correct" value="true" name="`q${index}-tf`" class="w-4 h-4 text-green-600">
                                    <span class="text-sm text-gray-700">True</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" v-model="question.correct" value="false" name="`q${index}-tf`" class="w-4 h-4 text-red-600">
                                    <span class="text-sm text-gray-700">False</span>
                                </label>
                            </div>
                            
                            <div v-if="question.type === 'fill_blank'" class="mt-3">
                                <label class="block text-sm text-gray-500 mb-1">Correct Answer</label>
                                <input v-model="question.correct" type="text" placeholder="Enter correct answer" 
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            
                            <div class="mt-4 flex items-center gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Points</label>
                                    <input v-model="question.points" type="number" min="1" 
                                        class="w-20 px-2 py-1 border border-gray-200 rounded-lg text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Quiz Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course *</label>
                            <select v-model="quiz.course_id" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select course</option>
                                <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit (minutes)</label>
                            <input v-model="quiz.time_limit" type="number" min="1" placeholder="30"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-xs text-gray-500">0 for no time limit</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Passing Score (%)</label>
                            <input v-model="quiz.passing_score" type="number" min="0" max="100" placeholder="70"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="quiz.status"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                        <hr class="border-gray-100">
                        <label class="flex items-center gap-2">
                            <input v-model="quiz.shuffle_questions" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">Shuffle Questions</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input v-model="quiz.show_results" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">Show Results After Submit</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input v-model="quiz.allow_retake" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                            <span class="text-sm text-gray-700">Allow Retakes</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { quizApi, courseApi } from '@/services/api';

const route = useRoute();
const router = useRouter();

const isEditing = computed(() => !!route.params.id);
const saving = ref(false);
const loading = ref(true);
const courses = ref([]);

const quiz = reactive({
    title: '',
    description: '',
    course_id: '',
    time_limit: 30,
    passing_score: 70,
    status: 'draft',
    shuffle_questions: false,
    show_results: true,
    allow_retake: true,
    questions: [],
});

function addQuestion(type) {
    quiz.questions.push({
        type,
        question: '',
        options: type === 'mcq' ? ['', '', '', ''] : [],
        correct: type === 'mcq' ? '' : type === 'true_false' ? 'true' : '',
        points: 1,
    });
}

function removeQuestion(index) {
    quiz.questions.splice(index, 1);
}

function addOption(questionIndex) {
    quiz.questions[questionIndex].options.push('');
}

function removeOption(questionIndex, optionIndex) {
    quiz.questions[questionIndex].options.splice(optionIndex, 1);
}

async function fetchCourses() {
    try {
        const { data } = await courseApi.getAll({ per_page: 100 });
        courses.value = data.data || [];
    } catch (error) {
        console.error('Error fetching courses:', error);
    }
}

async function fetchQuiz() {
    if (!route.params.id) return;
    loading.value = true;
    try {
        const { data } = await quizApi.getById(route.params.id);
        Object.assign(quiz, data);
    } catch (error) {
        console.error('Error fetching quiz:', error);
    } finally {
        loading.value = false;
    }
}

async function saveQuiz() {
    saving.value = true;
    try {
        if (isEditing.value) {
            await quizApi.update(route.params.id, quiz);
        } else {
            const { data } = await quizApi.create(quiz);
            router.push(`/instructor/quizzes/${data.id}/edit`);
            return;
        }
        router.push('/instructor/quizzes');
    } catch (error) {
        console.error('Error saving quiz:', error);
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    fetchCourses();
    fetchQuiz();
});
</script>