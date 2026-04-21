<template>
    <div class="relative overflow-hidden rounded-2xl" :class="fullHeight ? 'min-h-[600px]' : 'min-h-[400px]'">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-blue-900 to-indigo-800"></div>
        
        <div v-if="backgroundImage" class="absolute inset-0 opacity-30">
            <img :src="backgroundImage" class="w-full h-full object-cover" alt="Hero background">
        </div>

        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900/90 via-blue-900/70 to-indigo-600/50"></div>

        <div class="relative z-10 container mx-auto px-4 py-16 md:py-24 lg:py-32">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                    {{ title }}
                </h1>
                <p class="text-lg md:text-xl text-blue-100 mb-8 leading-relaxed">
                    {{ subtitle }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1 max-w-xl">
                        <input 
                            type="text"
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            :placeholder="searchPlaceholder"
                            class="w-full px-5 py-4 text-gray-900 bg-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-lg"
                        >
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button 
                        @click="handleSearch"
                        class="px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        Search
                    </button>
                </div>

                <div v-if="showStats" class="flex flex-wrap gap-8 mt-10 pt-8 border-t border-white/20">
                    <div>
                        <div class="text-3xl font-bold text-white">{{ stats.students }}+</div>
                        <div class="text-blue-200 text-sm">Students</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-white">{{ stats.courses }}+</div>
                        <div class="text-blue-200 text-sm">Courses</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-white">{{ stats.instructors }}+</div>
                        <div class="text-blue-200 text-sm">Instructors</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-white">{{ stats.rating }}+</div>
                        <div class="text-blue-200 text-sm">Rating</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -top-20 -left-20 w-60 h-60 bg-indigo-500/20 rounded-full blur-3xl"></div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
    title: {
        type: String,
        default: 'Learn Without Limits'
    },
    subtitle: {
        type: String,
        default: 'Start, switch, or advance your career with thousands of courses from expert instructors.'
    },
    searchPlaceholder: {
        type: String,
        default: 'What do you want to learn?'
    },
    backgroundImage: {
        type: String,
        default: ''
    },
    showStats: {
        type: Boolean,
        default: true
    },
    fullHeight: {
        type: Boolean,
        default: true
    },
    stats: {
        type: Object,
        default: () => ({
            students: '50K',
            courses: '2,500',
            instructors: '500',
            rating: '4.8'
        })
    }
});

const router = useRouter();
const searchQuery = ref('');

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ path: '/courses', query: { search: searchQuery.value.trim() } });
    }
};
</script>
