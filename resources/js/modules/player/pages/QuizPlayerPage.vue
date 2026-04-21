<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between max-w-4xl mx-auto">
        <div class="flex items-center gap-4">
          <button @click="goBack" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <div>
            <h1 class="text-lg font-semibold text-gray-900">{{ quiz.title }}</h1>
            <p class="text-sm text-gray-500">Question {{ currentQuestion + 1 }} of {{ quiz.questions?.length }}</p>
          </div>
        </div>
        
        <div class="flex items-center gap-4">
          <div class="text-right">
            <p class="text-sm text-gray-500">Time Remaining</p>
            <p class="text-lg font-semibold" :class="timeWarning ? 'text-red-600' : 'text-gray-900'">
              {{ formatTime(timeRemaining) }}
            </p>
          </div>
        </div>
      </div>
    </header>

    <!-- Progress Bar -->
    <div class="bg-white border-b border-gray-200 px-6 py-2">
      <div class="max-w-4xl mx-auto">
        <div class="h-1 bg-gray-200 rounded-full overflow-hidden">
          <div 
            class="h-full bg-blue-600 transition-all duration-300" 
            :style="{ width: `${((currentQuestion + 1) / (quiz.questions?.length || 1)) * 100}%` }"
          ></div>
        </div>
        <div class="flex gap-1 mt-2">
          <div 
            v-for="(q, i) in quiz.questions" 
            :key="i"
            class="w-2 h-2 rounded-full"
            :class="i === currentQuestion ? 'bg-blue-600' : answers[i] !== undefined ? 'bg-green-500' : 'bg-gray-300'"
          ></div>
        </div>
      </div>
    </div>

    <!-- Question -->
    <div class="max-w-4xl mx-auto px-6 py-8">
      <div v-if="currentQuestionData" class="bg-white rounded-2xl border border-gray-200 p-8">
        <div class="flex items-start gap-4 mb-6">
          <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-semibold">
            {{ currentQuestion + 1 }}
          </span>
          <h2 class="text-xl font-semibold text-gray-900 flex-1">{{ currentQuestionData.question }}</h2>
        </div>

        <!-- Options -->
        <div class="space-y-3">
          <button
            v-for="(option, i) in currentQuestionData.options"
            :key="i"
            @click="selectAnswer(i)"
            :class="[
              'w-full p-4 text-left rounded-xl border-2 transition-all flex items-center gap-4',
              answers[currentQuestion] === i 
                ? 'border-blue-500 bg-blue-50' 
                : 'border-gray-200 hover:border-gray-300'
            ]"
          >
            <span 
              class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-medium"
              :class="answers[currentQuestion] === i ? 'border-blue-500 bg-blue-500 text-white' : 'border-gray-300 text-gray-500'"
            >
              {{ String.fromCharCode(65 + i) }}
            </span>
            <span class="text-gray-700">{{ option }}</span>
          </button>
        </div>

        <!-- Navigation -->
        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
          <button 
            @click="prevQuestion" 
            :disabled="currentQuestion === 0"
            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          
          <button 
            v-if="currentQuestion < quiz.questions?.length - 1"
            @click="nextQuestion"
            :disabled="answers[currentQuestion] === undefined"
            class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
          
          <button 
            v-else
            @click="submitQuiz"
            :disabled="!allAnswered || submitting"
            class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ submitting ? 'Submitting...' : 'Submit Quiz' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Results Modal -->
    <div v-if="showResults" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 text-center">
        <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" :class="scoreClass">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="passing" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
          {{ passing ? 'Congratulations!' : 'Keep Trying!' }}
        </h2>
        
        <p class="text-gray-500 mb-6">
          You scored <span class="font-semibold text-gray-900">{{ correctAnswers }}</span> out of 
          <span class="font-semibold text-gray-900">{{ quiz.questions?.length }}</span> 
          ({{ Math.round((correctAnswers / quiz.questions?.length) * 100) }}%)
        </p>
        
        <div class="flex gap-3">
          <button @click="retryQuiz" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200">
            Retry Quiz
          </button>
          <button @click="goBack" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700">
            Continue
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { quizApi } from '../../../services/api';

const route = useRoute();
const router = useRouter();

const quiz = ref({ title: '', questions: [] });
const currentQuestion = ref(0);
const answers = ref({});
const timeRemaining = ref(0);
const timer = ref(null);
const submitting = ref(false);
const showResults = ref(false);
const correctAnswers = ref(0);

const currentQuestionData = computed(() => quiz.value.questions?.[currentQuestion.value]);
const allAnswered = computed(() => Object.keys(answers.value).length === quiz.value.questions?.length);
const timeWarning = computed(() => timeRemaining.value < 60);
const passing = computed(() => (correctAnswers.value / quiz.value.questions?.length) >= 0.7);
const scoreClass = computed(() => passing.value ? 'bg-green-500' : 'bg-red-500');

onMounted(async () => {
  try {
    const { data } = await quizApi.getById(route.params.id);
    quiz.value = data;
    timeRemaining.value = data.duration * 60;
    startTimer();
  } catch (error) {
    console.error('Error:', error);
  }
});

onUnmounted(() => {
  if (timer.value) clearInterval(timer.value);
});

function startTimer() {
  timer.value = setInterval(() => {
    timeRemaining.value--;
    if (timeRemaining.value <= 0) {
      submitQuiz();
    }
  }, 1000);
}

function formatTime(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function selectAnswer(index) {
  answers.value[currentQuestion.value] = index;
}

function nextQuestion() {
  if (currentQuestion.value < quiz.value.questions.length - 1) {
    currentQuestion.value++;
  }
}

function prevQuestion() {
  if (currentQuestion.value > 0) {
    currentQuestion.value--;
  }
}

function goBack() {
  router.push({ name: 'course-learn', params: { id: route.params.courseId } });
}

async function submitQuiz() {
  submitting.value = true;
  clearInterval(timer.value);
  
  try {
    const answerArray = Object.entries(answers.value).map(([q, a]) => ({ question: parseInt(q), answer: a }));
    const { data } = await quizApi.submit(route.params.id, { answers: answerArray });
    correctAnswers.value = data.correct;
    showResults.value = true;
  } catch (error) {
    console.error('Error:', error);
  } finally {
    submitting.value = false;
  }
}

function retryQuiz() {
  answers.value = {};
  currentQuestion.value = 0;
  showResults.value = false;
  timeRemaining.value = quiz.value.duration * 60;
  startTimer();
}
</script>