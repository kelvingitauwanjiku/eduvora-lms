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
                    <span class="text-white">Cookie Policy</span>
                </nav>
                <h1 class="text-4xl font-bold text-white">Cookie Policy</h1>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="prose prose-lg max-w-none">
                <h2>What Are Cookies</h2>
                <p>Cookies are small text files that are placed on your computer or mobile device when you visit a website. They are widely used to make websites work more efficiently and provide information to website owners.</p>
                
                <h2>How We Use Cookies</h2>
                <p>We use cookies to:</p>
                <ul>
                    <li>Keep you signed in</li>
                    <li>Understand how you use our platform</li>
                    <li>Improve your learning experience</li>
                    <li>Remember your preferences</li>
                    <li>Personalize content and recommendations</li>
                </ul>
                
                <h2>Types of Cookies We Use</h2>
                
                <h3>Essential Cookies</h3>
                <p>These cookies are necessary for the website to function. They enable you to navigate the site and use its features. You cannot opt out of these cookies.</p>
                
                <h3>Performance Cookies</h3>
                <p>These cookies collect information about how visitors use our website, such as which pages are most often visited. This helps us improve our service.</p>
                
                <h3>Functional Cookies</h3>
                <p>These cookies allow the website to remember choices you make and provide enhanced, personalized features.</p>
                
                <h3>Targeting Cookies</h3>
                <p>These cookies are used to deliver relevant advertisements that match your interests.</p>
                
                <h2>Managing Cookies</h2>
                <p>You can control or delete cookies as you wish. Most browsers allow you to block or delete cookies. However, note that if you block cookies, some features of our platform may not work properly.</p>
                
                <div class="bg-blue-50 rounded-xl p-6 mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Cookie Settings</h3>
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" checked class="w-5 h-5 text-blue-600 rounded" disabled>
                            <span class="text-gray-700">Essential (required for the site to work)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="analytics" class="w-5 h-5 text-blue-600 rounded">
                            <span class="text-gray-700">Analytics cookies</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="marketing" class="w-5 h-5 text-blue-600 rounded">
                            <span class="text-gray-700">Marketing cookies</span>
                        </label>
                    </div>
                    <button @click="savePreferences" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Save Preferences
                    </button>
                </div>
                
                <h2>Third-Party Cookies</h2>
                <p>We may also use third-party cookies from service providers to help us analyze how our platform is used. These parties have their own privacy policies.</p>
                
                <h2>Updates to This Policy</h2>
                <p>We may update this Cookie Policy from time to time. We will notify you of any material changes.</p>
                
                <h2>Contact Us</h2>
                <p>If you have questions about this Cookie Policy, please contact us.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const analytics = ref(true);
const marketing = ref(true);

function savePreferences() {
    console.log('Cookie preferences saved:', { analytics: analytics.value, marketing: marketing.value });
}

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
</style>