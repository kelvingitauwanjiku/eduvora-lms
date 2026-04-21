<template>
    <div>
        <!-- Breadcrumb -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-2 text-sm text-blue-100 mb-4">
                    <router-link to="/" class="hover:text-white transition">Home</router-link>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-white">Terms of Service</span>
                </nav>
                <h1 class="text-4xl font-bold text-white">Terms of Service</h1>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="prose prose-lg max-w-none" v-html="content"></div>
            
            <div class="mt-12 p-6 bg-gray-50 rounded-2xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Questions about these terms?</h3>
                <p class="text-gray-600 mb-4">If you have any questions about these Terms of Service, please contact us.</p>
                <router-link to="/contact" class="inline-flex items-center gap-2 text-blue-600 font-medium hover:text-blue-700 transition">
                    Contact Us
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const content = ref(`
    <h2>1. Acceptance of Terms</h2>
    <p>By accessing and using Eduvora, you accept and agree to be bound by the terms and provision of this agreement.</p>
    
    <h2>2. Description of Service</h2>
    <p>Eduvora provides users with access to a rich collection of resources, including various communications tools, forums, shopping services, personalized content, and branded programming through its network of properties.</p>
    
    <h2>3. Registration Obligations</h2>
    <p>You agree to provide accurate and complete registration information and to keep this information current. You are responsible for maintaining the security of your account.</p>
    
    <h2>4. Privacy Policy</h2>
    <p>Registration data and certain other information about you is subject to our Privacy Policy. You understand that through your use of Eduvora, you consent to the collection and use of this information.</p>
    
    <h2>5. User Conduct</h2>
    <p>You agree not to use Eduvora to:</p>
    <ul>
        <li>Upload, post, or otherwise transmit any content that is unlawful, harmful, threatening, abusive, or otherwise objectionable</li>
        <li>Harm minors in any way</li>
        <li>Impersonate any person or entity</li>
        <li>Engage in any activity that interferes with or disrupts Eduvora</li>
    </ul>
    
    <h2>6. Content Submitted</h2>
    <p>You are solely responsible for the content that you submit to Eduvora. By submitting content, you represent that you have all necessary rights to do so.</p>
    
    <h2>7. Limitation of Liability</h2>
    <p>Eduvora will not be liable for any indirect, incidental, or consequential damages arising out of your use of or inability to use the service.</p>
    
    <h2>8. Changes to Terms</h2>
    <p>Eduvora reserves the right to modify these terms at any time. Your continued use of the service constitutes acceptance of any modified terms.</p>
    
    <h2>9. Governing Law</h2>
    <p>These terms shall be governed by and construed in accordance with the laws of the jurisdiction in which Eduvora operates.</p>
    
    <h2>10. Contact Information</h2>
    <p>If you have any questions about these Terms of Service, please contact us through our support system.</p>
`);

onMounted(() => {
    window.scrollTo(0, 0);
});
</script>

<style scoped>
.prose h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.prose p {
    color: #4b5563;
    margin-bottom: 1rem;
    line-height: 1.625;
}
.prose ul {
    list-style: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    color: #4b5563;
}
.prose li {
    margin-bottom: 0.5rem;
}
</style>
