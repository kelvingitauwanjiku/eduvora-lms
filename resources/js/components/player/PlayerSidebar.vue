<template>
    <aside class="w-80 bg-white border-r h-full overflow-y-auto">
        <div class="p-4 border-b">
            <h3 class="font-semibold">Course Content</h3>
        </div>
        <div class="divide-y">
            <div v-for="section in sections" :key="section.id" class="p-4">
                <h4 class="font-medium text-sm text-gray-900 mb-2">{{ section.title }}</h4>
                <div class="space-y-1">
                    <button v-for="lesson in section.lessons" :key="lesson.id"
                        @click="$emit('select', lesson)"
                        class="w-full flex items-center gap-3 p-2 rounded-lg text-left transition"
                        :class="lesson.id === currentLessonId ? 'bg-blue-50 text-blue-600' : 'hover:bg-gray-50 text-gray-600'">
                        <svg v-if="lesson.completed" class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <svg v-else-if="lesson.type === 'video'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        </svg>
                        <svg v-else class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm truncate">{{ lesson.title }}</span>
                    </button>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup>
defineProps({
    sections: { type: Array, default: () => [] },
    currentLessonId: String,
});
defineEmits(['select']);
</script>