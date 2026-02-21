<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/Components/ui/badge';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    programStudy: Object,
});
</script>


<template>
    <Head :title="programStudy.name" />
    
    <PublicLayout>
        <div class="py-12 bg-white dark:bg-zinc-950">
             <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <article class="bg-white dark:bg-zinc-900 p-8 rounded-lg shadow-sm border border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-3 mb-6">
                        <Badge variant="outline" class="text-indigo-600 border-indigo-200">{{ programStudy.degree }}</Badge>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ programStudy.name }}</h1>
                    </div>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        <p v-if="programStudy.description" class="whitespace-pre-wrap leading-relaxed">{{ programStudy.description }}</p>
                        <p v-else class="italic text-gray-500">No description available.</p>
                    </div>
                </article>
            </div>
        </div>
    </PublicLayout>
</template>
