<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { GraduationCap, ArrowRight } from 'lucide-vue-next';
import { Badge } from '@/Components/ui/badge';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    programStudies: Array,
});
</script>

<template>
    <Head title="Program Studi" />

    <PublicLayout>
        <div class="py-12 bg-white dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Program Studi</h1>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Pilih program studi yang sesuai dengan passion dan cita-cita Anda di Fakultas Ilmu Kesehatan.
                    </p>
                </div>

                <div v-if="programStudies.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div 
                        v-for="ps in programStudies" 
                        :key="ps.id" 
                        class="group relative bg-white dark:bg-zinc-900 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-zinc-800 hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col h-full"
                    >
                        <div class="mb-6">
                            <Badge variant="secondary" class="mb-4 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border-none px-3 py-1 text-xs font-bold ring-1 ring-indigo-200">
                                {{ ps.degree }}
                            </Badge>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition">
                                {{ ps.name }}
                            </h2>
                        </div>
                        
                        <div class="prose prose-sm dark:prose-invert text-gray-600 dark:text-gray-400 mb-8 line-clamp-4 flex-1">
                            {{ ps.description || 'Pelajari lebih lanjut mengenai kurikulum, fasilitas, dan prospek karir di program studi ini.' }}
                        </div>

                        <div class="mt-auto">
                            <Link 
                                :href="route('public.program-studies.show', ps.slug)"
                                class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-700 transition group/link"
                            >
                                Lihat Selengkapnya 
                                <ArrowRight class="ml-2 w-4 h-4 transition-transform group-hover/link:translate-x-1" />
                            </Link>
                        </div>

                        <!-- Decoration Icon -->
                        <div class="absolute top-6 right-6 opacity-5 group-hover:opacity-10 transition">
                            <GraduationCap class="w-16 h-16" />
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-20 bg-gray-50 dark:bg-zinc-900/50 rounded-3xl border border-dashed border-gray-300 dark:border-zinc-800">
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Data program studi belum tersedia.</p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
