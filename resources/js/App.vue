<template>
    <KeepAlive>
        <component :is="layoutComponent" :key="layoutKey">
            <router-view />
        </component>
    </KeepAlive>
</template>

<script setup>
import { computed, onMounted, markRaw } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from './stores/auth';
import { useThemeStore } from './stores/theme';
import DefaultLayout from './layouts/DefaultLayout.vue';
import GuestLayout from './layouts/GuestLayout.vue';
import AdminLayout from './layouts/AdminLayout.vue';
import InstructorLayout from './layouts/InstructorLayout.vue';
import StudentLayout from './layouts/StudentLayout.vue';
import PlayerLayout from './layouts/PlayerLayout.vue';

const route = useRoute();
const authStore = useAuthStore();
const themeStore = useThemeStore();

const layouts = {
    default: markRaw(DefaultLayout),
    guest: markRaw(GuestLayout),
    admin: markRaw(AdminLayout),
    instructor: markRaw(InstructorLayout),
    student: markRaw(StudentLayout),
    player: markRaw(PlayerLayout),
};

const layoutKey = computed(() => route.fullPath);
const layoutComponent = computed(() => {
    const layoutName = route.meta?.layout || 'default';
    return layouts[layoutName] || layouts.default;
});

onMounted(() => {
    authStore.checkAuth();
    themeStore.initTheme();
});
</script>
