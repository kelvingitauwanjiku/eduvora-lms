import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authApi } from '../services/api';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const token = ref(localStorage.getItem('token'));
    const loading = ref(false);

    const isAuthenticated = computed(() => !!token.value);
    const isAdmin = computed(() => {
        if (user.value?.role === 'admin') return true;
        if (user.value?.roles?.some(r => r.slug === 'admin')) return true;
        return false;
    });
    const isInstructor = computed(() => {
        if (user.value?.role === 'instructor') return true;
        if (user.value?.is_instructor) return true;
        if (user.value?.roles?.some(r => r.slug === 'instructor')) return true;
        return false;
    });
    const isStudent = computed(() => !isAdmin.value && !isInstructor.value);

    async function checkAuth() {
        if (!token.value) return;
        try {
            const { data } = await authApi.me();
            user.value = data;
            if (data.roles) {
                localStorage.setItem('user', JSON.stringify(data));
            }
        } catch (error) {
            logout();
        }
    }

    async function login(credentials) {
        loading.value = true;
        try {
            const { data } = await authApi.login(credentials);
            token.value = data.token;
            user.value = data.user;
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            return data;
        } catch (error) {
            const data = error.response?.data;
            if (data?.errors) {
                const firstError = Object.values(data.errors)[0];
                throw Array.isArray(firstError) ? firstError[0] : firstError;
            }
            throw data?.message || 'Login failed';
        } finally {
            loading.value = false;
        }
    }

    async function register(userData) {
        loading.value = true;
        try {
            const { data } = await authApi.register(userData);
            token.value = data.token;
            user.value = data.user;
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            return data;
        } catch (error) {
            const data = error.response?.data;
            if (data?.errors) {
                const firstError = Object.values(data.errors)[0];
                throw Array.isArray(firstError) ? firstError[0] : firstError;
            }
            throw data?.message || 'Registration failed';
        } finally {
            loading.value = false;
        }
    }

    async function logout() {
        try {
            await authApi.logout();
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            token.value = null;
            user.value = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
        }
    }

    async function updateProfile(data) {
        loading.value = true;
        try {
            const { data: response } = await authApi.updateProfile(data);
            user.value = response;
            localStorage.setItem('user', JSON.stringify(response));
            return response;
        } finally {
            loading.value = false;
        }
    }

    async function forgotPassword(email) {
        loading.value = true;
        try {
            await authApi.sendResetLink({ email });
        } finally {
            loading.value = false;
        }
    }

    function initFromStorage() {
        const storedUser = localStorage.getItem('user');
        if (storedUser) {
            try {
                user.value = JSON.parse(storedUser);
            } catch (e) {
                user.value = null;
            }
        }
    }

    initFromStorage();

    return {
        user,
        token,
        loading,
        isAuthenticated,
        isAdmin,
        isInstructor,
        isStudent,
        checkAuth,
        login,
        register,
        logout,
        updateProfile,
        forgotPassword,
    };
});