<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BlockRenderer from '@/Components/PageBuilder/BlockRenderer.vue';
import { Calendar, Newspaper, ArrowRight } from 'lucide-vue-next';
import { Badge } from '@/Components/ui/badge';

const props = defineProps({
    page: Object,
    recentArticles: Array,
    recentEvents: Array,
    dynamicData: { type: Object, default: () => ({}) },
    isHomepage: { type: Boolean, default: false },
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const getImageUrl = (path) => {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
};
</script>

<template>
    <Head :title="page.meta_title || page.title" />

    <PublicLayout>
        <!-- Page Header (hidden in builder mode) -->
        <div v-if="page.editor_mode !== 'builder'" class="bg-gray-50 dark:bg-zinc-900/50 border-b">
            <div class="container mx-auto px-4 py-12 md:py-16">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-3xl md:text-5xl font-bold tracking-tight leading-tight text-gray-900 dark:text-white">
                        {{ page.title }}
                    </h1>
                </div>
            </div>
        </div>

        <!-- Builder Mode -->
        <div v-if="page.editor_mode === 'builder' && page.blocks && page.blocks.length > 0">
            <BlockRenderer :blocks="page.blocks" :dynamicData="dynamicData" />
        </div>

        <!-- Editor Mode -->
        <div v-else class="py-12">
            <!-- DEFAULT: Content + Sidebar Right -->
            <div v-if="!page.layout || page.layout === 'default'" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-8">
                        <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 md:p-10 text-gray-900 dark:text-gray-100 prose prose-lg dark:prose-invert max-w-none" v-html="page.content"></div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="lg:col-span-4 space-y-6">
                        <!-- Recent Articles Widget -->
                        <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 border shadow-sm sticky top-24">
                            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <Newspaper class="w-5 h-5 text-primary" />
                                Berita Terbaru
                            </h3>
                            <div v-if="recentArticles && recentArticles.length > 0" class="space-y-4">
                                <Link
                                    v-for="article in recentArticles"
                                    :key="article.id"
                                    :href="route('public.articles.show', article.slug)"
                                    class="group flex gap-3 items-start"
                                >
                                    <div v-if="article.image" class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200 shrink-0">
                                        <img :src="getImageUrl(article.image)" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <Badge v-if="article.category" variant="secondary" class="text-[10px] h-5 mb-1">{{ article.category.name }}</Badge>
                                        <h4 class="font-semibold text-sm leading-snug group-hover:text-primary transition-colors line-clamp-2">{{ article.title }}</h4>
                                        <span class="text-xs text-muted-foreground">{{ formatDate(article.created_at) }}</span>
                                    </div>
                                </Link>
                            </div>
                            <div v-else class="text-sm text-muted-foreground text-center py-4">Belum ada berita.</div>
                            <hr class="my-4 border-border" />
                            <Link :href="route('public.articles.index')" class="text-sm text-primary hover:underline flex items-center gap-1 font-medium">
                                Lihat Semua Berita <ArrowRight class="w-3 h-3" />
                            </Link>
                        </div>

                        <!-- Upcoming Events Widget -->
                        <div v-if="recentEvents && recentEvents.length > 0" class="bg-white dark:bg-zinc-900 rounded-xl p-6 border shadow-sm">
                            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <Calendar class="w-5 h-5 text-primary" />
                                Agenda
                            </h3>
                            <div class="space-y-3">
                                <Link
                                    v-for="event in recentEvents"
                                    :key="event.id"
                                    :href="route('public.events.show', event.slug)"
                                    class="group block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 transition"
                                >
                                    <p class="font-semibold text-sm leading-snug group-hover:text-primary transition-colors line-clamp-2">{{ event.title }}</p>
                                    <span class="text-xs text-muted-foreground flex items-center gap-1 mt-1">
                                        <Calendar class="w-3 h-3" />
                                        {{ formatDate(event.start_date) }}
                                    </span>
                                </Link>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            <!-- FULL WIDTH -->
            <div v-else-if="page.layout === 'full'" class="container mx-auto px-4">
                <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-10 text-gray-900 dark:text-gray-100 prose prose-lg dark:prose-invert max-w-none" v-html="page.content"></div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
