<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    groupedDocuments: Object,
});

function formatSize(filePath) {
    // In a real app we might get size from backend, 
    // but here we just show a download icon/text
    return 'Download';
}
</script>


<template>
    <Head title="Download Area" />
    
    <PublicLayout>
        <div class="py-12 bg-white dark:bg-zinc-950">
             <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Dokumen Akademik</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-8">Download area untuk mahasiswa dan dosen.</p>

                <div v-if="Object.keys(groupedDocuments).length > 0" class="space-y-8">
                    <div v-for="(docs, category) in groupedDocuments" :key="category" class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800 p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b pb-2">
                            {{ category || 'Umum' }}
                        </h2>
                        
                        <div class="space-y-4">
                            <div v-for="doc in docs" :key="doc.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-zinc-800/50 hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                                <div class="mb-4 sm:mb-0">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ doc.title }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ doc.description }}</div>
                                </div>
                                <div>
                                    <a :href="route('public.documents.download', doc.slug)" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-center py-12 bg-white dark:bg-zinc-900 rounded-lg border border-dashed border-gray-300">
                    <p class="text-gray-500 text-lg">Belum ada dokumen publik.</p>
                </div>
            </div>
            </div>
        </div>
    </PublicLayout>
</template>
