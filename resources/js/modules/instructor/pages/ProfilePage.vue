<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="mt-1 text-gray-500">Manage your instructor profile</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Profile Information</h2>
                    <form @submit.prevent="updateProfile" class="space-y-6">
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100">
                                <img :src="previewAvatar || form.avatar || '/images/avatar-placeholder.jpg'" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <input type="file" @change="handleAvatar" accept="image/*" 
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG. Max 2MB</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input v-model="form.name" type="text" 
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input v-model="form.email" type="email" disabled
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Professional Title</label>
                            <input v-model="form.title" type="text" placeholder="e.g., Senior Software Engineer"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500">Your professional title (e.g., "Senior Developer", "Marketing Expert")</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea v-model="form.bio" rows="4" placeholder="Tell students about yourself..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                            <p class="mt-1 text-sm text-gray-500">{{ form.bio?.length || 0 }}/500 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expertise/Skills</label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span v-for="(skill, index) in form.skills" :key="index" 
                                    class="flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">
                                    {{ skill }}
                                    <button type="button" @click="removeSkill(index)" class="hover:text-blue-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            </div>
                            <input v-model="newSkill" type="text" placeholder="Add a skill and press Enter"
                                @keydown.enter.prevent="addSkill"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <button type="submit" :disabled="saving" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            {{ saving ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Resume / CV</h2>
                    <div class="flex items-center gap-6">
                        <div class="flex-1 p-4 border border-dashed border-gray-300 rounded-xl">
                            <div v-if="form.resume" class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ form.resume_name }}</p>
                                    <p class="text-sm text-gray-500">PDF file</p>
                                </div>
                                <button type="button" @click="removeResume" class="text-red-600 hover:text-red-700">Remove</button>
                            </div>
                            <div v-else class="text-center py-4">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-sm text-gray-500">Upload your resume (PDF, Max 2MB)</p>
                            </div>
                        </div>
                        <input v-if="!form.resume" type="file" @change="handleResume" accept=".pdf" 
                            class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>
                    <form @submit.prevent="changePassword" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input v-model="passwordForm.current_password" type="password" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        </div>
                        <button type="submit" :disabled="changingPassword" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            {{ changingPassword ? 'Changing...' : 'Change Password' }}
                        </button>
                    </form>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Social Links</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🌐</span>
                                <input v-model="social.website" type="url" placeholder="https://example.com" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">📘</span>
                                <input v-model="social.facebook" type="url" placeholder="https://facebook.com/..." 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Twitter/X</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🐦</span>
                                <input v-model="social.twitter" type="url" placeholder="https://twitter.com/..." 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">💼</span>
                                <input v-model="social.linkedin" type="url" placeholder="https://linkedin.com/in/..." 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">YouTube</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">📺</span>
                                <input v-model="social.youtube" type="url" placeholder="https://youtube.com/..." 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <button @click="saveSocialLinks" type="button" :disabled="savingSocial"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50">
                            {{ savingSocial ? 'Saving...' : 'Save Social Links' }}
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Payout Settings</h2>
                    <div class="space-y-4">
                        <p class="text-sm text-gray-500">Configure how you receive payments</p>
                        <router-link to="/instructor/payouts" class="block w-full px-4 py-2 text-center border border-gray-200 rounded-lg hover:bg-gray-50">
                            Manage Payouts →
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { authApi, instructorApi } from '@/services/api';

const authStore = useAuthStore();

const saving = ref(false);
const changingPassword = ref(false);
const savingSocial = ref(false);
const previewAvatar = ref('');
const newSkill = ref('');

const form = reactive({
    name: '',
    email: '',
    title: '',
    bio: '',
    avatar: null,
    resume: null,
    resume_name: '',
    skills: [],
});

const passwordForm = reactive({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

const social = reactive({
    website: '',
    facebook: '',
    twitter: '',
    linkedin: '',
    youtube: '',
});

function handleAvatar(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }
        form.avatar = file;
        previewAvatar.value = URL.createObjectURL(file);
    }
}

function handleResume(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }
        if (file.type !== 'application/pdf') {
            alert('Only PDF files are allowed');
            return;
        }
        form.resume = file;
        form.resume_name = file.name;
    }
}

function removeResume() {
    form.resume = null;
    form.resume_name = '';
}

function addSkill() {
    if (newSkill.value && !form.skills.includes(newSkill.value)) {
        form.skills.push(newSkill.value);
        newSkill.value = '';
    }
}

function removeSkill(index) {
    form.skills.splice(index, 1);
}

async function updateProfile() {
    saving.value = true;
    try {
        const formData = new FormData();
        Object.keys(form).forEach(key => {
            if (key === 'avatar' && form[key] instanceof File) {
                formData.append(key, form[key]);
            } else if (key === 'skills') {
                formData.append(key, JSON.stringify(form[key]));
            } else if (key !== 'resume') {
                formData.append(key, form[key]);
            }
        });
        
        if (form.resume instanceof File) {
            formData.append('resume', form.resume);
        }
        
        await instructorApi.updateProfile(formData);
        authStore.setUser({ ...authStore.user, ...form });
        alert('Profile updated successfully!');
    } catch (error) {
        console.error('Error updating profile:', error);
    } finally {
        saving.value = false;
    }
}

async function changePassword() {
    if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
        alert('Passwords do not match');
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
    } finally {
        changingPassword.value = false;
    }
}

async function saveSocialLinks() {
    savingSocial.value = true;
    try {
        await instructorApi.updateSocialLinks(social);
        alert('Social links saved!');
    } catch (error) {
        console.error('Error saving social links:', error);
    } finally {
        savingSocial.value = false;
    }
}

onMounted(() => {
    if (authStore.user) {
        form.name = authStore.user.name || '';
        form.email = authStore.user.email || '';
        form.title = authStore.user.title || '';
        form.bio = authStore.user.bio || '';
        form.avatar = authStore.user.avatar || '';
        form.skills = authStore.user.skills || [];
    }
    
    instructorApi.getSocialLinks().then(({ data }) => {
        Object.assign(social, data);
    }).catch(console.error);
});
</script>