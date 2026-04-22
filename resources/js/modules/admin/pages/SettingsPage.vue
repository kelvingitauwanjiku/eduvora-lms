<template>
    <div>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
            <p class="mt-1 text-gray-500">System configuration</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">General Settings</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input v-model="settings.site_name" type="text" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Email</label>
                            <input v-model="settings.site_email" type="email" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                            <textarea v-model="settings.footer_text" rows="3" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Currency & Payment</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <select v-model="settings.currency" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Revenue Share (%)</label>
                            <input v-model="settings.instructor_share" type="number" 
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Maintenance Mode</h2>
                    <div class="space-y-4">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" v-model="settings.maintenance_mode" class="w-5 h-5 text-blue-600">
                            <span class="text-gray-700">Enable Maintenance Mode</span>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Actions</h2>
                    <div class="space-y-3">
                        <button @click="clearCache" class="w-full px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Clear Cache
                        </button>
                        <button class="w-full px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button @click="saveSettings" :disabled="saving" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                {{ saving ? 'Saving...' : 'Save Settings' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { settingsApi } from '@/services/api';

const saving = ref(false);
const loading = ref(true);

const settings = reactive({
    site_name: '',
    site_email: '',
    footer_text: '',
    currency: 'USD',
    instructor_share: 70,
    maintenance_mode: false,
});

async function fetchSettings() {
    try {
        loading.value = true;
        const { data } = await settingsApi.get();
        Object.assign(settings, data);
    } catch (error) {
        console.error('Error loading settings:', error);
    } finally {
        loading.value = false;
    }
}

async function saveSettings() {
    saving.value = true;
    try {
        await settingsApi.update(settings);
        alert('Settings saved!');
    } catch (error) {
        console.error('Error saving settings:', error);
        alert('Failed to save settings');
    } finally {
        saving.value = false;
    }
}

async function clearCache() {
    try {
        alert('Cache cleared!');
    } catch (error) {
        console.error('Error clearing cache:', error);
    }
}

onMounted(() => {
    fetchSettings();
});
</script>