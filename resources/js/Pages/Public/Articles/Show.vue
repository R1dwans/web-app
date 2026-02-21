<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Calendar, User, ArrowLeft } from 'lucide-vue-next';
import { Badge } from '@/Components/ui/badge';

const props = defineProps({
    article: Object,
    relatedArticles: Array,
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getImageUrl = (path) => {
    if (!path) return '/images/placeholder-news.jpg';
    return path.startsWith('http') ? path : `/storage/${path}`;
};
</script>

<template>
    <Head>
        <title>{{ article.title }}</title>
        <meta name="description" :content="article.meta_description || article.content.substring(0, 160).replace(/<[^>]*>?/gm, '')" />
        <meta name="keywords" :content="article.meta_keywords" />
        <meta property="og:title" :content="article.title" />
        <meta property="og:description" :content="article.meta_description" />
        <meta property="og:image" :content="getImageUrl(article.image)" />
        <meta property="og:type" content="article" />
    </Head>

    <PublicLayout>
        <!-- Article Header / Hero -->
        <div class="bg-gray-50 dark:bg-zinc-900/50 border-b">
            <div class="container mx-auto px-4 py-12 md:py-16">
                <div class="max-w-4xl mx-auto">
                    <!-- Breadcrumb / Back -->
                    <div class="mb-6">
                        <Link :href="route('public.articles.index')" class="text-sm text-muted-foreground hover:text-primary transition inline-flex items-center gap-1">
                            <ArrowLeft class="w-4 h-4" /> Kembali ke Berita
                        </Link>
                    </div>

                    <!-- Meta & Title -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3">
                            <Badge v-if="article.category">{{ article.category.name }}</Badge>
                            <span class="text-sm text-muted-foreground flex items-center gap-1">
                                <Calendar class="w-3 h-3" /> {{ formatDate(article.created_at) }}
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-bold tracking-tight leading-tight text-gray-900 dark:text-white">
                            {{ article.title }}
                        </h1>
                        <div class="flex items-center gap-3 pt-2">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <User class="w-5 h-5" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium">{{ article.user?.name || 'Admin' }}</span>
                                <span class="text-xs text-muted-foreground">Penulis</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <!-- DEFAULT: Content Left + Sidebar Right -->
            <div v-if="!article.layout || article.layout === 'default'" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <article class="lg:col-span-8">
                    <div v-if="article.image" class="rounded-xl overflow-hidden mb-10 shadow-lg border aspect-video">
                        <img :src="getImageUrl(article.image)" :alt="article.title" class="w-full h-full object-cover">
                    </div>
                    <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-bold prose-img:rounded-xl prose-a:text-primary" v-html="article.content"></div>
                </article>
                <aside class="lg:col-span-4 space-y-8">
                    <div class="bg-gray-50 dark:bg-zinc-900/50 rounded-xl p-6 border sticky top-24">
                        <h3 class="font-bold text-xl mb-6">Berita Terkait</h3>
                        <div v-if="relatedArticles.length > 0" class="space-y-6">
                            <Link v-for="related in relatedArticles" :key="related.id" :href="route('public.articles.show', related.slug)" class="group flex gap-4 items-start">
                                <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-200 shrink-0">
                                    <img :src="getImageUrl(related.image)" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Thumbnail">
                                </div>
                                <div class="space-y-1">
                                    <Badge variant="secondary" class="text-[10px] h-5">{{ related.category?.name }}</Badge>
                                    <h4 class="font-semibold text-sm leading-snug group-hover:text-primary transition-colors line-clamp-3">{{ related.title }}</h4>
                                    <span class="text-xs text-muted-foreground block">{{ formatDate(related.created_at) }}</span>
                                </div>
                            </Link>
                        </div>
                        <div v-else class="text-center py-8 text-muted-foreground text-sm">Tidak ada berita terkait saat ini.</div>
                        <hr class="my-6 border-border" />
                        <Link :href="route('public.articles.index')" class="block w-full text-center py-2 px-4 border rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                            Lihat Semua Berita
                        </Link>
                    </div>
                </aside>
            </div>

            <!-- FULL WIDTH -->
            <div v-else-if="article.layout === 'full'" class="max-w-none">
                <article>
                    <div v-if="article.image" class="rounded-xl overflow-hidden mb-10 shadow-lg border aspect-video max-h-[480px]">
                        <img :src="getImageUrl(article.image)" :alt="article.title" class="w-full h-full object-cover">
                    </div>
                    <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:font-bold prose-img:rounded-xl prose-a:text-primary" v-html="article.content"></div>
                </article>
            </div>
        </div>
    </PublicLayout>
</template>
