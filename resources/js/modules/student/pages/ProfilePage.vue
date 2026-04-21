<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="mt-1 text-gray-500">Manage your account settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Profile Information</h2>
                    <form @submit.prevent="updateProfile" class="space-y-6">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <img :src="form.avatar || '/images/avatar-placeholder.jpg'" class="w-20 h-20 rounded-full object-cover">
                                <label class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <input type="file" @change="handleAvatarChange" accept="image/*" class="hidden">
                                </label>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Profile Photo</p>
                                <p class="text-sm text-gray-500">JPG, PNG or GIF. Max 2MB.</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input v-model="form.name" type="text" 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input v-model="form.email" type="email" 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input v-model="form.phone" type="text" placeholder="+1 234 567 8900"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input v-model="form.dob" type="date"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea v-model="form.bio" rows="4" placeholder="Tell us about yourself..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                            <input v-model="form.linkedin" type="url" placeholder="https://linkedin.com/in/username"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
                            <input v-model="form.twitter" type="url" placeholder="https://twitter.com/username"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" :disabled="saving" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                            {{ saving ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>
                    <form @submit.prevent="changePassword" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input v-model="passwordForm.current_password" type="password" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input v-model="passwordForm.new_password" type="password" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input v-model="passwordForm.new_password_confirmation" type="password" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" :disabled="changingPassword" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition">
                            {{ changingPassword ? 'Changing...' : 'Change Password' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Notification Settings</h2>
                    <div class="space-y-4">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">Email notifications</span>
                            <input type="checkbox" v-model="notifications.email" class="w-5 h-5 text-blue-600 rounded">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">New course announcements</span>
                            <input type="checkbox" v-model="notifications.new_courses" class="w-5 h-5 text-blue-600 rounded">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">Promotional emails</span>
                            <input type="checkbox" v-model="notifications.promotions" class="w-5 h-5 text-blue-600 rounded">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="text-gray-700">Push notifications</span>
                            <input type="checkbox" v-model="notifications.push" class="w-5 h-5 text-blue-600 rounded">
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Status</h2>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-sm text-gray-700">Active</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Member since {{ memberSince }}</p>
                    <button class="w-full px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-sm">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { authApi, notificationApi } from '@/services/api';

const authStore = useAuthStore();

const saving = ref(false);
const changingPassword = ref(false);
const uploadingAvatar = ref(false);

const form = reactive({
    name: '',
    email: '',
    bio: '',
    avatar: '',
    phone: '',
    dob: '',
    linkedin: '',
    twitter: '',
});

const passwordForm = reactive({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const notifications = reactive({
    email: true,
    new_courses: true,
    promotions: false,
    push: true,
});

const memberSince = ref('');

onMounted(async () => {
    if (authStore.user) {
        form.name = authStore.user.name || '';
        form.email = authStore.user.email || '';
        form.bio = authStore.user.bio || '';
        form.avatar = authStore.user.avatar || '';
        form.phone = authStore.user.phone || '';
        form.dob = authStore.user.dob || '';
        form.linkedin = authStore.user.linkedin || '';
        form.twitter = authStore.user.twitter || '';
        memberSince.value = authStore.user.created_at ? new Date(authStore.user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'Unknown';
    }
    
    try {
        const { data } = await notificationApi.getSettings();
        if (data) {
            notifications.email = data.email ?? true;
            notifications.new_courses = data.new_courses ?? true;
            notifications.promotions = data.promotions ?? false;
            notifications.push = data.push ?? true;
        }
    } catch (error) {
        console.error('Error fetching notification settings:', error);
    }
});

async function handleAvatarChange(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be less than 2MB');
        return;
    }
    
    uploadingAvatar.value = true;
    const formData = new FormData();
    formData.append('avatar', file);
    
    try {
        const response = await authApi.updateProfile(formData);
        form.avatar = response.data.avatar || URL.createObjectURL(file);
        authStore.setUser({ ...authStore.user, avatar: form.avatar });
    } catch (error) {
        console.error('Error uploading avatar:', error);
        alert('Failed to upload avatar. Please try again.');
    } finally {
        uploadingAvatar.value = false;
    }
}

async function updateProfile() {
    saving.value = true;
    try {
        const { data } = await authApi.updateProfile(form);
        authStore.setUser({ ...authStore.user, ...data });
        alert('Profile updated successfully!');
    } catch (error) {
        console.error('Error updating profile:', error);
        alert('Failed to update profile. Please try again.');
    } finally {
        saving.value = false;
    }
}

async function changePassword() {
    if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
        alert('Passwords do not match!');
        return;
    }
    
    changingPassword.value = true;
    try {
        await authApi.changePassword(passwordForm);
        passwordForm.current_password = '';
        passwordForm.new_password = '';
        passwordForm.new_password_confirmation = '';
        alert('Password changed successfully!');
    } catch (error) {
        console.error('Error changing password:', error);
        alert(error.response?.data?.message || 'Failed to change password. Please check your current password.');
    } finally {
        changingPassword.value = false;
    }
}
</script>