<script setup>
import { ref, watch } from 'vue';
import { Button } from '@/Components/ui/button';
import { UploadCloud, X, Image as ImageIcon, Search } from 'lucide-vue-next';
import FileManagerModal from '@/Components/FileManagerModal.vue';

const props = defineProps({
    modelValue: [String, File, null],
    previewUrl: String,
    error: String,
});

const emit = defineEmits(['update:modelValue', 'change']);

const fileInput = ref(null);
const internalPreview = ref(props.previewUrl);
const isDragging = ref(false);
const showFileManager = ref(false);

watch(() => props.previewUrl, (newVal) => {
    internalPreview.value = newVal;
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    processFile(file);
};

const handleDrop = (event) => {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    processFile(file);
};

const processFile = (file) => {
    if (!file) return;

    // Simple validation
    if (!file.type.startsWith('image/')) {
        alert('Please upload an image file');
        return;
    }

    // Create local preview
    const reader = new FileReader();
    reader.onload = (e) => {
        internalPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);

    emit('update:modelValue', file);
    emit('change', file);
};

const removeImage = () => {
    internalPreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
    emit('update:modelValue', null);
    emit('change', null);
};

const triggerBrowse = () => {
    fileInput.value.click();
};

const openFileManager = () => {
    showFileManager.value = true;
};

const handleLibrarySelect = (url) => {
    internalPreview.value = url;
    emit('update:modelValue', url); // Emit URL string
    emit('change', url);
};
</script>

<template>
    <div class="w-full">
        <input 
            type="file" 
            ref="fileInput" 
            class="hidden" 
            accept="image/*" 
            @change="handleFileChange"
        />

        <!-- Preview State -->
        <div v-if="internalPreview" class="relative group rounded-lg overflow-hidden border bg-gray-100 dark:bg-zinc-900 aspect-video flex items-center justify-center">
            <img :src="internalPreview" class="w-full h-full object-cover" alt="Preview" />
            
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                <Button type="button" variant="secondary" size="sm" @click="triggerBrowse">Upload New</Button>
                <Button type="button" variant="secondary" size="sm" @click="openFileManager">Library</Button>
                <Button type="button" variant="destructive" size="sm" @click="removeImage">Remove</Button>
            </div>
        </div>

        <!-- Empty State / Dropzone -->
        <div 
            v-else
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            class="border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center text-center transition-colors bg-gray-50 hover:bg-gray-100 dark:bg-zinc-900 dark:hover:bg-zinc-800"
            :class="isDragging ? 'border-primary bg-primary/5' : 'border-gray-300 dark:border-gray-700'"
        >
            <div class="p-4 rounded-full bg-gray-100 dark:bg-zinc-800 mb-4 cursor-pointer" @click="triggerBrowse">
                <UploadCloud class="w-8 h-8 text-muted-foreground" />
            </div>
            <p class="text-sm font-medium mb-1">Click to upload or drag and drop</p>
            <p class="text-xs text-muted-foreground mb-4">SVG, PNG, JPG or GIF (max. 6MB)</p>
            
            <div class="flex items-center gap-2 w-full justify-center">
                <Button type="button" variant="outline" size="sm" @click="triggerBrowse" class="w-full max-w-[120px]">
                    Upload
                </Button>
                <span class="text-xs text-muted-foreground">OR</span>
                <Button type="button" variant="outline" size="sm" @click="openFileManager" class="w-full max-w-[120px]">
                    Library
                </Button>
            </div>
        </div>

        <p v-if="error" class="text-sm text-red-600 mt-2">{{ error }}</p>
        
        <FileManagerModal 
            v-model:open="showFileManager" 
            @select="handleLibrarySelect" 
        />
    </div>
</template>
