<script setup>
import { ref, watch, computed } from 'vue';
import { Button } from '@/Components/ui/button';
import { UploadCloud, X, FileText, FileSpreadsheet, File as FileIcon } from 'lucide-vue-next';

const props = defineProps({
    modelValue: [String, File, null],
    currentFileName: String,
    error: String,
    accept: {
        type: String,
        default: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx',
    },
    maxSizeMb: {
        type: Number,
        default: 6,
    },
    label: {
        type: String,
        default: 'PDF, DOCX, XLSX, PPTX',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const fileInput = ref(null);
const selectedFile = ref(null);
const isDragging = ref(false);

const displayFileName = computed(() => {
    if (selectedFile.value) return selectedFile.value.name;
    if (props.currentFileName) return props.currentFileName;
    return null;
});

const displayFileSize = computed(() => {
    if (!selectedFile.value) return null;
    const bytes = selectedFile.value.size;
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
});

const fileExtension = computed(() => {
    const name = displayFileName.value;
    if (!name) return '';
    return name.split('.').pop().toLowerCase();
});

const fileIconClass = computed(() => {
    const ext = fileExtension.value;
    if (['pdf'].includes(ext)) return 'text-red-500';
    if (['doc', 'docx'].includes(ext)) return 'text-blue-500';
    if (['xls', 'xlsx'].includes(ext)) return 'text-green-500';
    if (['ppt', 'pptx'].includes(ext)) return 'text-orange-500';
    return 'text-gray-500';
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

    // Size validation
    if (file.size > props.maxSizeMb * 1024 * 1024) {
        alert(`Ukuran file maksimal ${props.maxSizeMb}MB`);
        return;
    }

    selectedFile.value = file;
    emit('update:modelValue', file);
    emit('change', file);
};

const removeFile = () => {
    selectedFile.value = null;
    if (fileInput.value) fileInput.value.value = '';
    emit('update:modelValue', null);
    emit('change', null);
};

const triggerBrowse = () => {
    fileInput.value.click();
};
</script>

<template>
    <div class="w-full">
        <input 
            type="file" 
            ref="fileInput" 
            class="hidden" 
            :accept="accept" 
            @change="handleFileChange"
        />

        <!-- Preview State -->
        <div v-if="displayFileName" class="relative group rounded-lg overflow-hidden border bg-gray-50 dark:bg-zinc-900 p-4">
            <div class="flex items-center gap-4">
                <!-- File Icon -->
                <div class="flex-shrink-0 p-3 rounded-lg bg-white dark:bg-zinc-800 shadow-sm">
                    <FileText class="w-8 h-8" :class="fileIconClass" />
                </div>
                
                <!-- File Info -->
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm truncate">{{ displayFileName }}</p>
                    <p v-if="displayFileSize" class="text-xs text-muted-foreground mt-0.5">
                        {{ displayFileSize }}
                    </p>
                    <p v-else class="text-xs text-muted-foreground mt-0.5">File saat ini</p>
                </div>
                
                <!-- Actions -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <Button variant="outline" size="sm" @click="triggerBrowse">Ganti File</Button>
                    <Button variant="ghost" size="icon" class="h-8 w-8 text-red-500 hover:text-red-600 hover:bg-red-50" @click="removeFile">
                        <X class="w-4 h-4" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Empty State / Dropzone -->
        <div 
            v-else
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            class="border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center text-center transition-colors bg-gray-50 hover:bg-gray-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 cursor-pointer"
            :class="isDragging ? 'border-primary bg-primary/5' : 'border-gray-300 dark:border-gray-700'"
            @click="triggerBrowse"
        >
            <div class="p-4 rounded-full bg-gray-100 dark:bg-zinc-800 mb-4">
                <UploadCloud class="w-8 h-8 text-muted-foreground" />
            </div>
            <p class="text-sm font-medium mb-1">Klik untuk upload atau drag and drop</p>
            <p class="text-xs text-muted-foreground">{{ label }} (max. {{ maxSizeMb }}MB)</p>
        </div>

        <p v-if="error" class="text-sm text-red-600 mt-2">{{ error }}</p>
    </div>
</template>
