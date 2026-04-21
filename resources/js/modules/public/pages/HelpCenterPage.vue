<template>
    <div>
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl font-bold text-white mb-4">How can we help you?</h1>
                <p class="text-xl text-blue-100 mb-8">Search our knowledge base or browse topics below</p>
                
                <!-- Search -->
                <div class="max-w-2xl">
                    <div class="relative">
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Search for help..."
                            class="w-full px-6 py-4 pl-14 text-lg bg-white text-gray-900 rounded-2xl shadow-lg focus:outline-none focus:ring-2 focus:ring-white"
                        >
                        <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Categories -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Category Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <div v-for="category in filteredCategories" :key="category.title" 
                    class="bg-white rounded-2xl p-6 border border-gray-200 hover:shadow-lg hover:border-blue-300 transition cursor-pointer"
                    @click="selectedCategory = category">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4" :class="category.bgColor">
                        <svg class="w-7 h-7" :class="category.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="category.icon" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ category.title }}</h3>
                    <p class="text-gray-500 text-sm">{{ category.description }}</p>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <div v-for="(faq, index) in filteredFaqs" :key="index" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <button @click="toggleFaq(index)" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                            <span class="font-medium text-gray-900 pr-4">{{ faq.question }}</span>
                            <svg class="w-5 h-5 text-gray-500 shrink-0 transition-transform" :class="{ 'rotate-180': openIndex === index }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-if="openIndex === index" class="px-6 pb-4">
                            <p class="text-gray-600 leading-relaxed">{{ faq.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Still need help?</h2>
                <p class="text-gray-600 mb-6 max-w-xl mx-auto">Our support team is available 24/7 to assist you with any questions or issues.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <router-link to="/contact" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
                        Contact Us
                    </router-link>
                    <a href="mailto:support@eduvora.com" class="px-8 py-3 bg-white text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition border border-gray-200">
                        Email Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const searchQuery = ref('');
const openIndex = ref(null);
const selectedCategory = ref(null);

const categories = [
    {
        title: 'Getting Started',
        description: 'Learn how to create an account and start learning',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
        bgColor: 'bg-blue-100',
        iconColor: 'text-blue-600',
        faqs: [
            { question: 'How do I create an account?', answer: 'Click the "Sign Up" button in the top right corner and follow the registration process.' },
            { question: 'How do I enroll in a course?', answer: 'Browse our courses, select one you like, and click "Enroll" to get started.' },
            { question: 'Is there a free trial?', answer: 'Yes! You can preview courses for free before purchasing.' }
        ]
    },
    {
        title: 'Account & Billing',
        description: 'Manage your account settings and payments',
        icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        bgColor: 'bg-green-100',
        iconColor: 'text-green-600',
        faqs: [
            { question: 'How do I update my payment method?', answer: 'Go to Settings > Billing > Payment Methods to add or update your cards.' },
            { question: 'Can I get a refund?', answer: 'Yes, we offer a 30-day money-back guarantee for most courses.' },
            { question: 'How do I cancel my subscription?', answer: 'Visit your account settings to manage or cancel your subscription.' }
        ]
    },
    {
        title: 'Technical Issues',
        description: 'Troubleshoot problems with the platform',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        bgColor: 'bg-red-100',
        iconColor: 'text-red-600',
        faqs: [
            { question: 'Why isn\'t my video loading?', answer: 'Try refreshing the page or checking your internet connection.' },
            { question: 'How do I report a bug?', answer: 'Contact our support team with details about the issue.' }
        ]
    },
    {
        title: 'Courses & Learning',
        description: 'Everything about course content and progress',
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        bgColor: 'bg-purple-100',
        iconColor: 'text-purple-600',
        faqs: [
            { question: 'How do I track my progress?', answer: 'Your progress is automatically tracked in your dashboard.' },
            { question: 'Can I download course materials?', answer: 'Yes, most courses have downloadable resources.' },
            { question: 'How do I get a certificate?', answer: 'Complete all course requirements to receive your certificate.' }
        ]
    },
    {
        title: 'Certificates',
        description: 'Learn about earning and using certificates',
        icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        bgColor: 'bg-yellow-100',
        iconColor: 'text-yellow-600',
        faqs: [
            { question: 'How do I receive my certificate?', answer: 'Certificates are automatically issued upon course completion.' },
            { question: 'Are certificates verified?', answer: 'Yes, all certificates can be verified through our platform.' }
        ]
    },
    {
        title: 'For Instructors',
        description: 'Information for course creators',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        bgColor: 'bg-indigo-100',
        iconColor: 'text-indigo-600',
        faqs: [
            { question: 'How do I become an instructor?', answer: 'Apply through your dashboard to become an instructor.' },
            { question: 'How do I get paid?', answer: 'Instructors receive payments through our payout system.' }
        ]
    }
];

const allFaqs = [
    { question: 'How do I create an account?', answer: 'Click the "Sign Up" button in the top right corner and follow the registration process.' },
    { question: 'How do I enroll in a course?', answer: 'Browse our courses, select one you like, and click "Enroll" to get started.' },
    { question: 'Is there a free trial?', answer: 'Yes! You can preview courses for free before purchasing.' },
    { question: 'How do I update my payment method?', answer: 'Go to Settings > Billing > Payment Methods to add or update your cards.' },
    { question: 'Can I get a refund?', answer: 'Yes, we offer a 30-day money-back guarantee for most courses.' },
    { question: 'How do I cancel my subscription?', answer: 'Visit your account settings to manage or cancel your subscription.' },
    { question: 'Why isn\'t my video loading?', answer: 'Try refreshing the page or checking your internet connection.' },
    { question: 'How do I track my progress?', answer: 'Your progress is automatically tracked in your dashboard.' },
    { question: 'How do I get a certificate?', answer: 'Complete all course requirements to receive your certificate.' }
];

const filteredCategories = computed(() => {
    if (!searchQuery.value) return categories;
    const query = searchQuery.value.toLowerCase();
    return categories.filter(cat => 
        cat.title.toLowerCase().includes(query) ||
        cat.description.toLowerCase().includes(query) ||
        cat.faqs.some(faq => faq.question.toLowerCase().includes(query))
    );
});

const filteredFaqs = computed(() => {
    if (!searchQuery.value) return allFaqs;
    const query = searchQuery.value.toLowerCase();
    return allFaqs.filter(faq => 
        faq.question.toLowerCase().includes(query) ||
        faq.answer.toLowerCase().includes(query)
    );
});

function toggleFaq(index) {
    openIndex.value = openIndex.value === index ? null : index;
}
</script>
