<template>
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
        <EnhancedTopBar v-bind="topBarConfig" @language-change="handleLanguageChange" />
        <AppHeader />

        <main class="flex-1 flex items-center justify-center p-6">
            <slot />
        </main>

        <AppFooter phone="+1 234 567 890" email="support@eduvora.com" />
    </div>
</template>

<script setup>
import EnhancedTopBar from '../components/layout/EnhancedTopBar.vue';
import AppHeader from '../components/layout/AppHeader.vue';
import AppFooter from '../components/layout/AppFooter.vue';
import { topBarConfig } from '../config/topbar';

function handleLanguageChange(lang) {
    console.log('Language changed to:', lang);
}
</script>
