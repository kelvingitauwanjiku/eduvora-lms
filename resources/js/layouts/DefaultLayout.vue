<template>
    <div class="min-h-screen bg-gray-50">
        <EnhancedTopBar v-bind="topBarConfig" @language-change="handleLanguageChange" />
        <AppHeader />
        <main>
            <router-view />
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
    // Future: integrate with i18n, API call to save preference, reload content
}
</script>
