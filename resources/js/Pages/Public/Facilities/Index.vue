<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    facilities: Array,
});
</script>


<template>
    <Head title="Facilities & Services" />
    
    <PublicLayout>
        <div class="py-12 bg-white dark:bg-zinc-950">
             <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Fasilitas & Layanan</h1>

                    <div v-if="facilities.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="facility in facilities" :key="facility.id" class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden flex flex-col group hover:shadow-md transition">
                            <div class="h-48 overflow-hidden bg-gray-200 dark:bg-gray-800">
                                <img v-if="facility.image" :src="`/storage/${facility.image}`" :alt="facility.title" class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-300" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 transition">
                                    <Link :href="route('public.facilities.show', facility.slug)">{{ facility.title }}</Link>
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                    {{ facility.description || 'Tidak ada deskripsi.' }}
                                </p>
                                
                                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-800 text-sm text-gray-500 space-y-1">
                                    <div class="flex items-center gap-2" v-if="facility.location">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ facility.location }}
                                    </div>
                                    <div class="flex items-center gap-2" v-if="facility.capacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Kapasitas: {{ facility.capacity }} orang
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-12 bg-white dark:bg-zinc-900 rounded-lg border border-dashed border-gray-300">
                        <p class="text-gray-500 text-lg">Belum ada data fasilitas.</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
