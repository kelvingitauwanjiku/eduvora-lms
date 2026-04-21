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
                    <span class="text-white">Privacy Policy</span>
                </nav>
                <h1 class="text-4xl font-bold text-white">Privacy Policy</h1>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-600 mb-8">Last updated: {{ lastUpdated }}</p>
                
                <h2>1. Introduction</h2>
                <p>At Eduvora, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.</p>
                
                <h2>2. Information We Collect</h2>
                
                <h3>Personal Information</h3>
                <p>We may collect personal information that you voluntarily provide to us when you:</p>
                <ul>
                    <li>Register on the platform</li>
                    <li>Enroll in a course</li>
                    <li>Create content</li>
                    <li>Contact our support</li>
                </ul>
                <p>This information may include your name, email address, phone number, payment information, and learning preferences.</p>
                
                <h3>Automatically Collected Information</h3>
                <p>We automatically collect certain information when you visit, use, or navigate the platform. This information may include device and usage data such as your IP address, browser type, operating system, and interaction with the platform.</p>
                
                <h2>3. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide and maintain our services</li>
                    <li>Track your course progress</li>
                    <li>Communicate with you about courses and updates</li>
                    <li>Process your transactions</li>
                    <li>Improve our platform and user experience</li>
                    <li>Comply with legal obligations</li>
                </ul>
                
                <h2>4. Sharing Your Information</h2>
                <p>We may share your information with:</p>
                <ul>
                    <li><strong>Instructors:</strong> Course creators receive necessary information to deliver their courses</li>
                    <li><strong>Service Providers:</strong> Third parties who assist in our operations</li>
                    <li><strong>Legal Authorities:</strong> When required by law or to protect rights</li>
                </ul>
                
                <h2>5. Data Security</h2>
                <p>We implement appropriate technical and organizational security measures to protect your personal information. However, no method of transmission over the internet is 100% secure.</p>
                
                <h2>6. Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal information</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Object to processing</li>
                    <li>Export your data</li>
                </ul>
                
                <h2>7. Children's Privacy</h2>
                <p>Our platform is not intended for children under 13. We do not knowingly collect personal information from children under 13.</p>
                
                <h2>8. Third-Party Links</h2>
                <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of these external sites.</p>
                
                <h2>9. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of any material changes.</p>
                
                <h2>10. Contact Us</h2>
                <p>If you have questions about this Privacy Policy, please contact us through our support system.</p>
            </div>
            
            <div class="mt-12 p-6 bg-gray-50 rounded-2xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Questions about privacy?</h3>
                <p class="text-gray-600 mb-4">If you have any questions about this Privacy Policy, please contact our Data Protection Officer.</p>
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
import { computed, onMounted } from 'vue';

const lastUpdated = computed(() => {
    const date = new Date();
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
});

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
.prose h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
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
.prose strong {
    color: #111827;
}
</style>