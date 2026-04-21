<template>
    <div>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Certificates</h1>
                <p class="mt-1 text-gray-500">Design and manage course certificates</p>
            </div>
            <button @click="showBuilder = true" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Certificate
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="cert in certificates" :key="cert.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-blue-50 to-blue-100 p-6 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-xs text-blue-600 uppercase tracking-widest mb-2">Certificate</div>
                        <div class="text-sm font-medium text-gray-900">{{ cert.name }}</div>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-500">{{ cert.courses_count || 0 }} courses using</p>
                    <div class="flex gap-2 mt-3">
                        <router-link :to="`/admin/certificates/${cert.id}/edit`" class="text-sm text-blue-600 hover:text-blue-700">Edit</router-link>
                        <button class="text-sm text-red-600 hover:text-red-700">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Builder Modal -->
        <div v-if="showBuilder" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl w-full max-w-4xl max-h-[90vh] overflow-auto">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Certificate Builder</h2>
                    <button @click="showBuilder = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Certificate Name</label>
                        <input v-model="builder.name" type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                            <input v-model="builder.backgroundColor" type="color" class="w-full h-12 border border-gray-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Border Color</label>
                            <input v-model="builder.borderColor" type="color" class="w-full h-12 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Certificate Text</label>
                        <textarea v-model="builder.content" rows="4" class="w-full px-4 py-2 border border-gray-200 rounded-lg"></textarea>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                    <button @click="showBuilder = false" class="px-4 py-2 border border-gray-200 rounded-lg">Cancel</button>
                    <button @click="saveCertificate" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';

const showBuilder = ref(false);
const certificates = ref([]);
const builder = reactive({ name: '', backgroundColor: '#ffffff', borderColor: '#3b82f6', content: '' });

onMounted(() => {});
</script>