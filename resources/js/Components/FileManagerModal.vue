<script setup>
import { ref, watch, onMounted } from 'vue';
import { Button } from '@/Components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/Components/ui/dialog'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Image as ImageIcon, UploadCloud, CheckCircle2 } from 'lucide-vue-next';
import axios from 'axios';
import ImageUpload from '@/Components/ImageUpload.vue';

const props = defineProps({
    open: Boolean,
});

const emit = defineEmits(['update:open', 'select']);

const activeTab = ref('library');
const images = ref([]);
const selectedImage = ref(null);
const isLoading = ref(false);
const uploadImageFile = ref(null);
const uploadError = ref('');

const fetchImages = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('media.index'));
        images.value = response.data;
    } catch (error) {
        console.error('Failed to fetch media:', error);
    } finally {
        isLoading.value = false;
    }
};

watch(() => props.open, (isOpen) => {
    if (isOpen) {
        fetchImages();
        activeTab.value = 'library';
        selectedImage.value = null;
        uploadImageFile.value = null;
        uploadError.value = '';
    }
});

const selectImage = (image) => {
    selectedImage.value = image;
};

const confirmSelection = () => {
    if (selectedImage.value) {
        emit('select', selectedImage.value.url);
        emit('update:open', false);
    }
};

const handleUploadSuccess = async (file) => {
    // We reuse the existing upload endpoint 
    // In a real scenario, we might want a dedicated media upload, 
    // but for now we can simulate "uploading" by using the article upload logic 
    // OR just use the ImageUpload component's value to trigger an upload
    
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    try {
        isLoading.value = true;
        const response = await axios.post(route('articles.upload-image'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        // Refresh library and select the new image
        await fetchImages();
        
        // Find the newly uploaded image (assuming it's the latest, or strictly matching URL)
        const uploadedUrl = response.data.url;
        const found = images.value.find(img => img.url === uploadedUrl);
        if (found) {
            selectedImage.value = found;
            activeTab.value = 'library';
        } else {
             // Fallback if URL doesn't essentially match list (e.g. timestamp diffs)
             // Just select the first one as we sorted by new
             if (images.value.length > 0) selectedImage.value = images.value[0];
             activeTab.value = 'library';
        }

    } catch (error) {
        console.error('Upload failed:', error);
        uploadError.value = 'Upload failed. Please try again.';
    } finally {
        isLoading.value = false;
    }
};

// Watch for file selection in Upload tab
watch(uploadImageFile, (newFile) => {
    if (newFile) {
        handleUploadSuccess(newFile);
    }
});

</script>

<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-3xl h-[600px] flex flex-col p-0 gap-0">
            <DialogHeader class="p-6 border-b">
                <DialogTitle>File Manager</DialogTitle>
                <DialogDescription>
                    Select an image from your library or upload a new one.
                </DialogDescription>
            </DialogHeader>

            <div class="flex-1 overflow-hidden">
                <Tabs v-model="activeTab" class="h-full flex flex-col">
                    <div class="px-6 pt-4">
                        <TabsList class="grid w-full grid-cols-2 max-w-[400px]">
                            <TabsTrigger value="library">Library</TabsTrigger>
                            <TabsTrigger value="upload">Upload</TabsTrigger>
                        </TabsList>
                    </div>

                    <TabsContent value="library" class="flex-1 overflow-y-auto p-6 mt-0">
                        <div v-if="isLoading && images.length === 0" class="flex items-center justify-center h-full">
                            <span class="text-muted-foreground">Loading specific images...</span>
                        </div>
                        <div v-else-if="images.length === 0" class="flex flex-col items-center justify-center h-full text-muted-foreground">
                            <ImageIcon class="w-12 h-12 mb-2 opacity-50" />
                            <p>No images found.</p>
                        </div>
                        <div v-else class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                            <div 
                                v-for="image in images" 
                                :key="image.path"
                                class="aspect-square relative group cursor-pointer border rounded-md overflow-hidden bg-gray-100 dark:bg-zinc-800"
                                :class="{'ring-2 ring-primary ring-offset-2': selectedImage && selectedImage.path === image.path}"
                                @click="selectImage(image)"
                            >
                                <img :src="image.url" class="w-full h-full object-cover transition-transform group-hover:scale-105" loading="lazy" />
                                <div v-if="selectedImage && selectedImage.path === image.path" class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                    <CheckCircle2 class="w-8 h-8 text-white drop-shadow-md" />
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] p-1 truncate px-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ image.name }}
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    <TabsContent value="upload" class="flex-1 p-6 flex flex-col items-center justify-center mt-0">
                         <div class="w-full max-w-md">
                            <ImageUpload v-model="uploadImageFile" :error="uploadError" />
                            <p class="text-center text-sm text-muted-foreground mt-4">
                                Select an image to upload it to the library.
                            </p>
                         </div>
                    </TabsContent>
                </Tabs>
            </div>

            <DialogFooter class="p-4 border-t bg-gray-50 dark:bg-zinc-900">
                <Button variant="outline" type="button" @click="$emit('update:open', false)">Cancel</Button>
                <Button type="button" :disabled="!selectedImage" @click="confirmSelection">
                    Insert Selected
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
