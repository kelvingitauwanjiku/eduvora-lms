<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Certificates</h1>
            <p class="mt-1 text-gray-500">Certificates you've earned</p>
        </div>

        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="i in 6" :key="i" class="animate-pulse">
                <div class="bg-gray-200 h-48 rounded-xl"></div>
            </div>
        </div>

        <div v-else-if="certificates.length === 0" class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.241-.247 3.47 3.47 0 014.438 0 3.42 3.42 0 001.241.247 3.42 3.42 0 012.814.013 3.47 3.47 0 012.814.013 3.42 3.42 0 001.241.247c.85.215 1.525.85 1.741 1.764 1.166.49 1.914 1.597 1.914 2.855 0 .912-.485 1.743-1.278 2.197a3.42 3.42 0 00-.506 1.837"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">No certificates yet</h3>
            <p class="text-gray-500 mt-1">Complete a course to earn your first certificate</p>
            <router-link to="/courses" class="mt-4 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Browse Courses
            </router-link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="cert in certificates" :key="cert.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition">
                <div class="aspect-[1.4/1] bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 p-6 flex flex-col items-center justify-center text-white relative">
                    <div class="absolute top-4 right-4 opacity-20">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-xs uppercase tracking-widest opacity-80 mb-2">Certificate of Completion</p>
                        <div class="w-16 h-16 border-2 border-white/30 rounded-full mx-auto mb-2 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.241-.247 3.47 3.47 0 014.438 0 3.42 3.42 0 001.241.247 3.42 3.42 0 012.814.013 3.47 3.47 0 012.814.013 3.42 3.42 0 001.241.247c.85.215 1.525.85 1.741 1.764"/>
                            </svg>
                        </div>
                        <p class="text-xs opacity-60 mt-2">{{ cert.course?.title?.substring(0, 30) || 'Course' }}...</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Issued on</p>
                            <p class="font-medium text-gray-900">{{ formatDate(cert.issued_at) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="downloadCertificate(cert)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Download PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </button>
                            <button @click="shareCertificate(cert)" class="p-2 text-gray-500 hover:bg-gray-50 rounded-lg" title="Share">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Certificate ID: {{ cert.id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { certificateApi } from '@/services/api';

const loading = ref(true);
const certificates = ref([]);

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

async function fetchCertificates() {
    loading.value = true;
    try {
        const { data } = await certificateApi.getAll();
        certificates.value = data.data || [];
    } catch (error) {
        console.error('Error fetching certificates:', error);
    } finally {
        loading.value = false;
    }
}

async function downloadCertificate(cert) {
    try {
        const response = await certificateApi.download(cert.id);
        if (response.data.url) {
            window.open(response.data.url, '_blank');
        } else if (response.data.download_url) {
            window.open(response.data.download_url, '_blank');
        } else if (response.data.file) {
            const link = document.createElement('a');
            link.href = `data:application/pdf;base64,${response.data.file}`;
            link.download = `certificate-${cert.id}.pdf`;
            link.click();
        }
    } catch (error) {
        console.error('Error downloading certificate:', error);
        alert('Unable to download certificate. Please try again.');
    }
}

function shareCertificate(cert) {
    const shareUrl = `${window.location.origin}/verify-certificate/${cert.id}`;
    if (navigator.share) {
        navigator.share({
            title: 'My Certificate',
            text: `I completed ${cert.course?.title}`,
            url: shareUrl
        });
    } else {
        navigator.clipboard.writeText(shareUrl);
        alert('Certificate link copied to clipboard!');
    }
}

onMounted(() => {
    fetchCertificates();
});
</script>