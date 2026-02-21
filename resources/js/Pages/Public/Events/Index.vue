<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    events: Array,
});

function formatDate(dateString) {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function formatTime(dateString) {
    if (!dateString) return '';
    return new Date(dateString).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>


<template>
    <Head title="Agenda & Events" />
    
    <PublicLayout>
        <div class="py-12 bg-white dark:bg-zinc-950">
             <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Agenda & Events</h1>

                    <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="event in events" :key="event.id" class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden flex flex-col group hover:shadow-md transition">
                            <div class="h-48 overflow-hidden bg-gray-100 dark:bg-zinc-800 relative">
                                <img v-if="event.image" :src="`/storage/${event.image}`" :alt="event.title" class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-300" />
                                <div v-else class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-md text-xs font-bold text-indigo-600 shadow-sm">
                                    {{ formatDate(event.start_date) }}
                                </div>
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 transition">
                                    <Link :href="route('public.events.show', event.slug)">{{ event.title }}</Link>
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3 flex-1">{{ event.description }}</p>
                                
                                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-800 text-sm text-gray-500">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ formatTime(event.start_date) }}
                                        <span v-if="event.end_date"> - {{ formatTime(event.end_date) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2" v-if="event.location">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ event.location }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-12 bg-white dark:bg-zinc-900 rounded-lg border border-dashed border-gray-300">
                        <p class="text-gray-500 text-lg">Tidak ada agenda mendatang.</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
