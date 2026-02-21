<script setup>
import { useForm, Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Settings, Globe, Phone, Share2, Save, Upload, Check, Home } from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Label } from '@/Components/ui/label';

const props = defineProps({
    settings: Object,
    pages: Array,
});

const activeTab = ref('general');

const tabs = [
    { key: 'general', label: 'Umum', icon: Globe },
    { key: 'homepage', label: 'Beranda', icon: Home },
    { key: 'contact', label: 'Kontak', icon: Phone },
    { key: 'social', label: 'Sosial Media', icon: Share2 },
];

// Build reactive form data from all settings
const formData = ref({});
const fileInputs = ref({});

// Initialize form data
Object.values(props.settings).forEach(group => {
    group.forEach(setting => {
        formData.value[setting.key] = setting.value || '';
    });
});

const isSubmitting = ref(false);
const showSuccess = ref(false);

const handleSubmit = () => {
    isSubmitting.value = true;

    const data = new FormData();

    // Add all text/textarea values
    Object.entries(formData.value).forEach(([key, value]) => {
        data.append(`settings[${key}]`, value ?? '');
    });

    // Add file inputs
    Object.entries(fileInputs.value).forEach(([key, file]) => {
        if (file) {
            data.append(`files[${key}]`, file);
        }
    });

    router.post(route('settings.update'), data, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            isSubmitting.value = false;
            showSuccess.value = true;
            setTimeout(() => showSuccess.value = false, 3000);
        },
        onError: () => {
            isSubmitting.value = false;
        },
    });
};

const handleFileChange = (key, event) => {
    const file = event.target.files[0];
    if (file) {
        fileInputs.value[key] = file;
    }
};

const getPreviewUrl = (setting) => {
    if (fileInputs.value[setting.key]) {
        return URL.createObjectURL(fileInputs.value[setting.key]);
    }
    if (setting.value) {
        return `/storage/${setting.value}`;
    }
    return null;
};
</script>

<template>
    <Head title="Pengaturan" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 rounded-lg">
                            <Settings class="w-6 h-6 text-indigo-600" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Aplikasi</h1>
                            <p class="text-sm text-gray-500">Kelola konfigurasi situs Anda</p>
                        </div>
                    </div>
                    <Button @click="handleSubmit" :disabled="isSubmitting" class="gap-2">
                        <Check v-if="showSuccess" class="w-4 h-4" />
                        <Save v-else class="w-4 h-4" />
                        {{ showSuccess ? 'Tersimpan!' : 'Simpan Perubahan' }}
                    </Button>
                </div>

                <!-- Tabs -->
                <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl mb-8">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-sm font-medium transition-all"
                        :class="activeTab === tab.key 
                            ? 'bg-white text-indigo-600 shadow-sm' 
                            : 'text-gray-500 hover:text-gray-700'"
                    >
                        <component :is="tab.icon" class="w-4 h-4" />
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Settings Form -->
                <form @submit.prevent="handleSubmit" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <template v-for="tab in tabs" :key="tab.key">
                        <div v-show="activeTab === tab.key" class="p-6 space-y-6">
                            <!-- Custom homepage settings UI -->
                            <template v-if="tab.key === 'homepage'">
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Tampilan Beranda</h3>
                                        <p class="text-sm text-gray-500 mb-4">Tentukan apa yang ditampilkan di halaman utama situs Anda.</p>
                                    </div>

                                    <!-- Display Mode Selection -->
                                    <div class="space-y-3">
                                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                                            :class="formData['homepage_display'] === 'default' ? 'border-indigo-500 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300'"
                                            @click="formData['homepage_display'] = 'default'"
                                        >
                                            <input type="radio" v-model="formData['homepage_display']" value="default" class="mt-0.5 text-indigo-600" />
                                            <div>
                                                <span class="font-medium text-gray-900">Beranda Default</span>
                                                <p class="text-sm text-gray-500 mt-0.5">Tampilkan halaman selamat datang dengan slider, artikel terbaru, dan agenda.</p>
                                            </div>
                                        </label>

                                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                                            :class="formData['homepage_display'] === 'page' ? 'border-indigo-500 bg-indigo-50/50' : 'border-gray-200 hover:border-gray-300'"
                                            @click="formData['homepage_display'] = 'page'"
                                        >
                                            <input type="radio" v-model="formData['homepage_display']" value="page" class="mt-0.5 text-indigo-600" />
                                            <div class="flex-1">
                                                <span class="font-medium text-gray-900">Halaman Statis</span>
                                                <p class="text-sm text-gray-500 mt-0.5">Gunakan salah satu halaman yang sudah dibuat (seperti WordPress).</p>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Page Selector (shown when 'page' mode) -->
                                    <div v-if="formData['homepage_display'] === 'page'" class="ml-8 space-y-3">
                                        <Label class="text-sm font-medium text-gray-700">Pilih Halaman</Label>
                                        <select v-model="formData['homepage_page_id']" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
                                            <option value="">— Pilih halaman —</option>
                                            <option v-for="page in pages" :key="page.id" :value="String(page.id)">
                                                {{ page.title }} {{ page.editor_mode === 'builder' ? '(Page Builder)' : '' }}
                                            </option>
                                        </select>

                                        <!-- Selected page info -->
                                        <div v-if="formData['homepage_page_id'] && pages.find(p => String(p.id) === formData['homepage_page_id'])" class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                                                <Check class="w-4 h-4 text-green-600" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-green-800">{{ pages.find(p => String(p.id) === formData['homepage_page_id'])?.title }}</p>
                                                <p class="text-xs text-green-600">/{{ pages.find(p => String(p.id) === formData['homepage_page_id'])?.slug }}</p>
                                            </div>
                                        </div>

                                        <p v-if="!pages || pages.length === 0" class="text-sm text-amber-600 bg-amber-50 p-3 rounded-lg">
                                            ⚠️ Belum ada halaman yang dipublikasikan. Buat halaman terlebih dahulu di menu Pages.
                                        </p>
                                    </div>
                                </div>
                            </template>

                            <!-- Generic settings rendering -->
                            <template v-else-if="settings[tab.key]">
                                <div v-for="setting in settings[tab.key]" :key="setting.key" class="space-y-2">
                                    <Label :for="setting.key" class="text-sm font-medium text-gray-700">
                                        {{ setting.label }}
                                    </Label>

                                    <!-- Text Input -->
                                    <Input 
                                        v-if="setting.type === 'text'"
                                        :id="setting.key"
                                        v-model="formData[setting.key]"
                                        type="text"
                                        :placeholder="`Masukkan ${setting.label.toLowerCase()}`"
                                    />

                                    <!-- Textarea -->
                                    <Textarea 
                                        v-else-if="setting.type === 'textarea'"
                                        :id="setting.key"
                                        v-model="formData[setting.key]"
                                        rows="3"
                                        :placeholder="`Masukkan ${setting.label.toLowerCase()}`"
                                    />

                                    <!-- Image Upload -->
                                    <div v-else-if="setting.type === 'image'" class="space-y-3">
                                        <div v-if="getPreviewUrl(setting)" class="relative w-48 h-20 rounded-lg overflow-hidden border bg-gray-50">
                                            <img :src="getPreviewUrl(setting)" class="w-full h-full object-contain" />
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <label :for="setting.key" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                                                <Upload class="w-4 h-4" />
                                                Pilih Gambar
                                            </label>
                                            <input 
                                                :id="setting.key"
                                                type="file"
                                                accept="image/*"
                                                class="hidden"
                                                @change="handleFileChange(setting.key, $event)"
                                            />
                                            <span v-if="fileInputs[setting.key]" class="text-sm text-green-600">
                                                {{ fileInputs[setting.key].name }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Boolean Toggle -->
                                    <div v-else-if="setting.type === 'boolean'" class="flex items-center">
                                        <button 
                                            type="button"
                                            @click="formData[setting.key] = formData[setting.key] === '1' ? '0' : '1'"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                            :class="formData[setting.key] === '1' ? 'bg-indigo-600' : 'bg-gray-200'"
                                        >
                                            <span 
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                :class="formData[setting.key] === '1' ? 'translate-x-5' : 'translate-x-0'"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="text-center py-10 text-gray-400">
                                Belum ada pengaturan untuk kategori ini.
                            </div>
                        </div>
                    </template>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
