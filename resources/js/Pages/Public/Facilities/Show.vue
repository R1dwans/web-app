<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { MapPin, Users, ArrowLeft } from 'lucide-vue-next';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    facility: Object,
});
</script>

<template>
    <Head :title="facility.title" />

    <PublicLayout>
        <div class="py-12 bg-white dark:bg-zinc-950">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <!-- Back Button -->
                <Link :href="route('public.facilities.index')" class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 mb-8 transition">
                    <ArrowLeft class="w-4 h-4 mr-2" /> Kembali ke Fasilitas
                </Link>

                <article class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-800 overflow-hidden">
                    <!-- Featured Image -->
                    <div v-if="facility.image" class="w-full h-80 overflow-hidden">
                        <img :src="`/storage/${facility.image}`" :alt="facility.title" class="w-full h-full object-cover" />
                    </div>

                    <div class="p-8 md:p-12">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                            {{ facility.title }}
                        </h1>

                        <!-- Meta Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10 pb-10 border-b border-gray-100 dark:border-zinc-800">
                            <div v-if="facility.location" class="flex items-start gap-3">
                                <div class="p-2 bg-indigo-50 dark:bg-zinc-800 rounded-lg text-indigo-600">
                                    <MapPin class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Lokasi</p>
                                    <p class="text-gray-900 dark:text-gray-300 font-medium">{{ facility.location }}</p>
                                </div>
                            </div>

                            <div v-if="facility.capacity" class="flex items-start gap-3">
                                <div class="p-2 bg-indigo-50 dark:bg-zinc-800 rounded-lg text-indigo-600">
                                    <Users class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Kapasitas</p>
                                    <p class="text-gray-900 dark:text-gray-300 font-medium">{{ facility.capacity }} Orang</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            <div v-if="facility.description" class="whitespace-pre-wrap leading-relaxed">{{ facility.description }}</div>
                            <p v-else class="italic text-gray-500">Tidak ada deskripsi untuk fasilitas ini.</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </PublicLayout>
</template>
