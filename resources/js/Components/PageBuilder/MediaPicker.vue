<script setup>
/**
 * MediaPicker - Modal component for browsing existing media and uploading new images.
 * Used in PageBuilder for Image, Gallery, and Hero blocks.
 */
import { ref, computed, onMounted, watch } from 'vue';
import { X, Upload, Search, Image, Loader2, Check, Trash2, FolderOpen } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    show: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'select']);

const images = ref([]);
const loading = ref(false);
const uploading = ref(false);
const searchQuery = ref('');
const selectedImages = ref([]);
const activeTab = ref('browse'); // browse, upload
const dragOver = ref(false);
const filterDir = ref('all');

const fetchImages = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('media.index'));
        images.value = response.data;
    } catch (e) {
        console.error('Failed to fetch media:', e);
    } finally {
        loading.value = false;
    }
};

watch(() => props.show, (val) => {
    if (val) {
        fetchImages();
        selectedImages.value = [];
        searchQuery.value = '';
    }
});

const filteredImages = computed(() => {
    let list = images.value;
    if (filterDir.value !== 'all') {
        list = list.filter(img => img.directory === filterDir.value);
    }
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        list = list.filter(img => img.name.toLowerCase().includes(q));
    }
    return list;
});

const directories = computed(() => {
    const dirs = [...new Set(images.value.map(img => img.directory))];
    return dirs;
});

const toggleSelect = (img) => {
    if (props.multiple) {
        const idx = selectedImages.value.findIndex(s => s.path === img.path);
        if (idx >= 0) selectedImages.value.splice(idx, 1);
        else selectedImages.value.push(img);
    } else {
        selectedImages.value = [img];
    }
};

const isSelected = (img) => selectedImages.value.some(s => s.path === img.path);

const confirmSelection = () => {
    if (selectedImages.value.length === 0) return;
    if (props.multiple) {
        emit('select', selectedImages.value);
    } else {
        emit('select', selectedImages.value[0]);
    }
    emit('close');
};

const handleUpload = async (files) => {
    if (!files || files.length === 0) return;
    uploading.value = true;
    
    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('directory', 'pages');
        
        try {
            const response = await axios.post(route('media.upload'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            images.value.unshift(response.data);
            // Auto-select uploaded image
            if (!props.multiple) {
                selectedImages.value = [response.data];
            } else {
                selectedImages.value.push(response.data);
            }
        } catch (e) {
            console.error('Upload failed:', e);
        }
    }
    
    uploading.value = false;
    activeTab.value = 'browse';
};

const onFileInput = (e) => {
    handleUpload(e.target.files);
    e.target.value = '';
};

const onDrop = (e) => {
    e.preventDefault();
    dragOver.value = false;
    handleUpload(e.dataTransfer.files);
};

const formatSize = (bytes) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="show" class="fixed inset-0 z-[99999] flex items-center justify-center p-4" @click.self="$emit('close')">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                
                <!-- Modal -->
                <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-zinc-700 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center">
                                <Image class="w-5 h-5 text-primary" />
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Media Library</h3>
                                <p class="text-xs text-muted-foreground">Pilih gambar atau upload baru</p>
                            </div>
                        </div>
                        <button @click="$emit('close')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-400 hover:text-gray-600 transition">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Toolbar -->
                    <div class="flex items-center gap-3 px-6 py-3 border-b border-gray-100 dark:border-zinc-800 shrink-0">
                        <!-- Tabs -->
                        <div class="flex bg-gray-100 dark:bg-zinc-800 p-0.5 rounded-lg">
                            <button @click="activeTab = 'browse'" class="px-3 py-1.5 rounded-md text-xs font-medium transition-all" :class="activeTab === 'browse' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-500'">
                                <FolderOpen class="w-3.5 h-3.5 inline mr-1" />Jelajahi
                            </button>
                            <button @click="activeTab = 'upload'" class="px-3 py-1.5 rounded-md text-xs font-medium transition-all" :class="activeTab === 'upload' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-500'">
                                <Upload class="w-3.5 h-3.5 inline mr-1" />Upload
                            </button>
                        </div>

                        <template v-if="activeTab === 'browse'">
                            <!-- Search -->
                            <div class="flex-1 relative">
                                <Search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input v-model="searchQuery" type="text" placeholder="Cari gambar..." class="w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-zinc-800 border-0 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                            </div>

                            <!-- Filter -->
                            <select v-model="filterDir" class="px-3 py-2 bg-gray-50 dark:bg-zinc-800 border-0 rounded-lg text-xs focus:ring-2 focus:ring-primary/20 outline-none">
                                <option value="all">Semua</option>
                                <option v-for="dir in directories" :key="dir" :value="dir">{{ dir }}</option>
                            </select>
                        </template>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <!-- Browse Tab -->
                        <template v-if="activeTab === 'browse'">
                            <div v-if="loading" class="flex items-center justify-center py-20">
                                <Loader2 class="w-8 h-8 text-primary animate-spin" />
                            </div>
                            <div v-else-if="filteredImages.length === 0" class="text-center py-20">
                                <Image class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                                <p class="text-gray-500 text-sm">Tidak ada gambar ditemukan</p>
                                <button @click="activeTab = 'upload'" class="mt-3 text-primary text-sm font-medium hover:underline">Upload gambar baru</button>
                            </div>
                            <div v-else class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
                                <button 
                                    v-for="img in filteredImages" 
                                    :key="img.path" 
                                    @click="toggleSelect(img)"
                                    class="group relative aspect-square rounded-xl overflow-hidden border-2 transition-all hover:shadow-lg"
                                    :class="isSelected(img) ? 'border-primary ring-2 ring-primary/30 scale-95' : 'border-transparent hover:border-gray-300'"
                                >
                                    <img :src="img.url" :alt="img.name" class="w-full h-full object-cover" loading="lazy" />
                                    <!-- Selected Overlay -->
                                    <div v-if="isSelected(img)" class="absolute inset-0 bg-primary/30 flex items-center justify-center">
                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center shadow-lg">
                                            <Check class="w-5 h-5 text-white" />
                                        </div>
                                    </div>
                                    <!-- Info on hover -->
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent px-2 py-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <p class="text-white text-[10px] truncate">{{ img.name }}</p>
                                        <p class="text-white/70 text-[9px]">{{ formatSize(img.size) }}</p>
                                    </div>
                                </button>
                            </div>
                        </template>

                        <!-- Upload Tab -->
                        <template v-else>
                            <div 
                                @dragover.prevent="dragOver = true" 
                                @dragleave="dragOver = false" 
                                @drop="onDrop"
                                class="border-2 border-dashed rounded-2xl p-16 text-center transition-all"
                                :class="dragOver ? 'border-primary bg-primary/5 scale-[1.01]' : 'border-gray-300 dark:border-zinc-600 hover:border-gray-400'"
                            >
                                <div v-if="uploading" class="flex flex-col items-center">
                                    <Loader2 class="w-12 h-12 text-primary animate-spin mb-4" />
                                    <p class="font-medium text-gray-600">Mengupload...</p>
                                </div>
                                <template v-else>
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                                        <Upload class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-1">Drag & drop gambar di sini</h4>
                                    <p class="text-sm text-gray-500 mb-4">atau</p>
                                    <label class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-medium cursor-pointer hover:bg-primary/90 transition shadow-lg hover:shadow-xl hover:scale-105">
                                        <Upload class="w-4 h-4" />
                                        Pilih File
                                        <input type="file" accept="image/*" :multiple="multiple" @change="onFileInput" class="hidden" />
                                    </label>
                                    <p class="text-xs text-gray-400 mt-3">JPG, PNG, GIF, WebP, SVG — max 5MB</p>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50 shrink-0">
                        <p class="text-xs text-muted-foreground">
                            <template v-if="selectedImages.length > 0">
                                {{ selectedImages.length }} gambar dipilih
                            </template>
                            <template v-else>
                                Klik gambar untuk memilih
                            </template>
                        </p>
                        <div class="flex gap-2">
                            <button @click="$emit('close')" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition">
                                Batal
                            </button>
                            <button @click="confirmSelection" :disabled="selectedImages.length === 0" class="px-6 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-40 disabled:cursor-not-allowed transition shadow-sm">
                                Pilih Gambar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .relative, .modal-leave-to .relative { transform: scale(0.95); }
</style>
