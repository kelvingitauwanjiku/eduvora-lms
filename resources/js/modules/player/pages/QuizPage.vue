<template>
    <PlayerLayout>
        <div class="min-h-screen flex items-center justify-center bg-gray-900 p-8">
            <div class="max-w-3xl w-full">
                <!-- Quiz Header -->
                <div v-if="!submitted" class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-white">{{ quiz.title || 'Quiz' }}</h1>
                            <p class="text-gray-400 mt-1">Question {{ currentIndex + 1 }} of {{ quiz.questions?.length || 0 }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400">Time Remaining</p>
                            <p class="text-2xl font-bold" :class="timeRemaining < 60 ? 'text-red-500' : 'text-white'">
                                {{ formatTime(timeRemaining) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 rounded-full transition-all duration-300" 
                            :style="{ width: ((currentIndex + 1) / (quiz.questions?.length || 1) * 100) + '%' }">
                        </div>
                    </div>
                </div>

                <!-- Results Screen -->
                <div v-if="submitted" class="bg-gray-800 rounded-2xl p-8 text-center">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full flex items-center justify-center" 
                        :class="score >= passingScore ? 'bg-green-500/20' : 'bg-red-500/20'">
                        <svg v-if="score >= passingScore" class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg v-else class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    
                    <h2 class="text-3xl font-bold text-white mb-2">
                        {{ score >= passingScore ? 'Congratulations!' : 'Keep Trying!' }}
                    </h2>
                    <p class="text-gray-400 mb-6">
                        {{ score >= passingScore ? 'You passed the quiz!' : `You need ${passingScore}% to pass.` }}
                    </p>
                    
                    <div class="bg-gray-700 rounded-xl p-6 mb-8">
                        <div class="text-5xl font-bold text-white mb-2">{{ score }}%</div>
                        <p class="text-gray-400">{{ correctAnswers }} of {{ quiz.questions?.length }} correct</p>
                    </div>
                    
                    <!-- Score Breakdown -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="bg-green-500/10 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-400">{{ correctAnswers }}</div>
                            <div class="text-sm text-gray-400">Correct</div>
                        </div>
                        <div class="bg-red-500/10 rounded-lg p-4">
                            <div class="text-2xl font-bold text-red-400">{{ wrongAnswers }}</div>
                            <div class="text-sm text-gray-400">Wrong</div>
                        </div>
                        <div class="bg-gray-500/10 rounded-lg p-4">
                            <div class="text-2xl font-bold text-gray-400">{{ skippedAnswers }}</div>
                            <div class="text-sm text-gray-400">Skipped</div>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 justify-center">
                        <button @click="retry" class="px-8 py-3 bg-gray-700 text-white rounded-xl hover:bg-gray-600 transition">
                            Retry Quiz
                        </button>
                        <button @click="goBack" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                            Back to Course
                        </button>
                    </div>
                </div>

                <!-- Quiz Questions -->
                <div v-else-if="currentQuestion" class="bg-gray-800 rounded-2xl p-8">
                    <div class="mb-8">
                        <p class="text-lg text-white font-medium mb-2">{{ currentQuestion.question }}</p>
                        <p v-if="currentQuestion.description" class="text-gray-400 text-sm">{{ currentQuestion.description }}</p>
                    </div>

                    <!-- MCQ Options -->
                    <div v-if="currentQuestion.type === 'mcq'" class="space-y-3">
                        <label v-for="(option, index) in currentQuestion.options" :key="index"
                            class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all"
                            :class="answers[currentIndex] === option 
                                ? 'border-blue-500 bg-blue-500/10' 
                                : 'border-gray-600 hover:border-gray-500 hover:bg-gray-700'">
                            <input type="radio" :name="`q${currentIndex}`" :value="option" v-model="answers[currentIndex]"
                                class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                            <span class="text-white">{{ option }}</span>
                        </label>
                    </div>

                    <!-- True/False Options -->
                    <div v-else-if="currentQuestion.type === 'true_false'" class="space-y-3">
                        <label class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all"
                            :class="answers[currentIndex] === 'true' 
                                ? 'border-blue-500 bg-blue-500/10' 
                                : 'border-gray-600 hover:border-gray-500 hover:bg-gray-700'">
                            <input type="radio" v-model="answers[currentIndex]" value="true"
                                class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white">True</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all"
                            :class="answers[currentIndex] === 'false' 
                                ? 'border-blue-500 bg-blue-500/10' 
                                : 'border-gray-600 hover:border-gray-500 hover:bg-gray-700'">
                            <input type="radio" v-model="answers[currentIndex]" value="false"
                                class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-white">False</span>
                            </div>
                        </label>
                    </div>

                    <!-- Fill in the Blank -->
                    <div v-else-if="currentQuestion.type === 'fill_blank'">
                        <input v-model="answers[currentIndex]" type="text" placeholder="Type your answer..."
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Question Navigation -->
                    <div class="mt-8 pt-6 border-t border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button v-for="(_, idx) in quiz.questions" :key="idx" @click="currentIndex = idx"
                                :class="idx === currentIndex ? 'bg-blue-600' : answers[idx] !== undefined ? 'bg-green-600' : 'bg-gray-600'"
                                class="w-8 h-8 rounded-lg text-sm font-medium text-white hover:opacity-80 transition">
                                {{ idx + 1 }}
                            </button>
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="previousQuestion" :disabled="currentIndex === 0"
                                class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                Previous
                            </button>
                            <button v-if="currentIndex < (quiz.questions?.length || 1) - 1" @click="nextQuestion"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Next
                            </button>
                            <button v-else @click="confirmSubmit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Submit Quiz
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-else-if="loadingQuiz" class="bg-gray-800 rounded-2xl p-12 flex items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>

        <!-- Submit Confirmation Modal -->
        <div v-if="showConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4">
                <h3 class="text-xl font-bold text-white mb-4">Submit Quiz?</h3>
                <p class="text-gray-400 mb-6">
                    You have answered {{ answeredCount }} of {{ quiz.questions?.length }} questions.
                    <span v-if="skippedAnswers > 0" class="text-yellow-400"> ({{ skippedAnswers }} skipped)</span>
                </p>
                <div class="flex gap-4 justify-end">
                    <button @click="showConfirmModal = false"
                        class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                        Continue Quiz
                    </button>
                    <button @click="submitQuiz"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </PlayerLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import PlayerLayout from '@/layouts/PlayerLayout.vue';
import api from '@/services/api';

const route = useRoute();
const router = useRouter();

const courseId = computed(() => route.params.id);
const quizId = computed(() => route.params.quizId);

const loadingQuiz = ref(true);
const quiz = ref({ title: '', questions: [] });
const currentIndex = ref(0);
const answers = ref([]);
const timeRemaining = ref(0);
const submitted = ref(false);
const score = ref(0);
const correctAnswers = ref(0);
const wrongAnswers = ref(0);
const skippedAnswers = ref(0);
const showConfirmModal = ref(false);
const passingScore = ref(70);
let timer = null;

const currentQuestion = computed(() => quiz.value.questions?.[currentIndex.value]);
const answeredCount = computed(() => answers.value.filter(a => a !== undefined && a !== '').length);

function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

async function fetchQuiz() {
    loadingQuiz.value = true;
    try {
        const { data } = await api.get(`/quizzes/${quizId.value}`);
        quiz.value = data;
        answers.value = new Array(data.questions?.length || 0);
        timeRemaining.value = data.duration * 60 || 1800;
        passingScore.value = data.passing_score || 70;
    } catch (error) {
        console.error('Error fetching quiz:', error);
        quiz.value = {
            title: 'Sample Quiz',
            questions: [
                { id: 1, question: 'What is Vue.js?', type: 'mcq', options: ['A library', 'A framework', 'A language', 'A database'], correct: 'A framework' },
                { id: 2, question: 'Is Vue.js reactive?', type: 'true_false', correct: 'true' },
                { id: 3, question: 'Complete: Vue uses a ____ architecture', type: 'fill_blank', correct: 'component' },
            ],
        };
        answers.value = new Array(quiz.value.questions.length);
        timeRemaining.value = 1800;
    } finally {
        loadingQuiz.value = false;
    }
}

function previousQuestion() {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
}

function nextQuestion() {
    if (currentIndex.value < quiz.value.questions.length - 1) {
        currentIndex.value++;
    }
}

function confirmSubmit() {
    showConfirmModal.value = true;
}

function submitQuiz() {
    showConfirmModal.value = false;
    correctAnswers.value = 0;
    wrongAnswers.value = 0;
    skippedAnswers.value = 0;
    
    quiz.value.questions.forEach((q, i) => {
        const answer = answers.value[i];
        if (answer === undefined || answer === '') {
            skippedAnswers.value++;
        } else if (String(answer).toLowerCase() === String(q.correct).toLowerCase()) {
            correctAnswers.value++;
        } else {
            wrongAnswers.value++;
        }
    });
    
    score.value = Math.round((correctAnswers.value / quiz.value.questions.length) * 100);
    submitted.value = true;
    
    if (timer) clearInterval(timer);
    
    api.post(`/quizzes/${quizId.value}/submit`, {
        answers: answers.value,
        score: score.value,
        time_taken: (quiz.value.duration * 60) - timeRemaining.value,
    }).catch(() => {});
}

function retry() {
    answers.value = new Array(quiz.value.questions.length);
    currentIndex.value = 0;
    submitted.value = false;
    score.value = 0;
    correctAnswers.value = 0;
    wrongAnswers.value = 0;
    skippedAnswers.value = 0;
    timeRemaining.value = quiz.value.duration * 60 || 1800;
    timer = setInterval(() => {
        if (timeRemaining.value > 0) timeRemaining.value--;
    }, 1000);
}

function goBack() {
    router.push(`/student/courses/${courseId.value}/learn`);
}

onMounted(() => {
    fetchQuiz();
    timer = setInterval(() => {
        if (timeRemaining.value > 0 && !submitted.value) {
            timeRemaining.value--;
        }
    }, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>
