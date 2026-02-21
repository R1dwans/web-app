<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, watch } from 'vue';
import { Search, Calendar, User, ArrowRight, ChevronRight, Tag } from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardFooter, CardHeader } from '@/Components/ui/card';
import debounce from 'lodash/debounce';

const props = defineProps({
    articles: Object,
    categories: Array,
    recentArticles: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');

// Debounced search
const performSearch = debounce((value) => {
    router.get(route('public.articles.index'), { 
        search: value,
        category: props.filters.category 
    }, { 
        preserveState: true, 
        replace: true 
    });
}, 300);

watch(search, (value) => {
    performSearch(value);
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getImageUrl = (path) => {
    if (!path) return '/images/placeholder-news.jpg'; // Ensure you have a placeholder
    return path.startsWith('http') ? path : `/storage/${path}`;
};
</script>

<template>
    <Head title="Berita & Artikel" description="Berita terbaru dan artikel menarik dari Fakultas Ilmu Kesehatan." />

    <PublicLayout>
        <!-- Hero Section -->
        <div class="relative bg-primary/5 py-16 md:py-24">
            <div class="container mx-auto px-4 text-center">
                <Badge variant="secondary" class="mb-4">Informasi Terkini</Badge>
                <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4 text-gray-900 dark:text-white">
                    Berita & Artikel
                </h1>
                <p class="text-lg text-muted-foreground max-w-2xl mx-auto mb-8">
                    Temukan informasi terbaru, kegiatan akademik, dan artikel ilmiah dari lingkungan Fakultas Ilmu Kesehatan.
                </p>
                
                <!-- Search Bar (Centered) -->
                <div class="max-w-md mx-auto relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                    <Input 
                        v-model="search" 
                        placeholder="Cari berita..." 
                        class="pl-10 h-12 rounded-full shadow-sm bg-background border-muted"
                    />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-12 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                
                <!-- Left Sidebar (Categories & Recent) -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Categories -->
                    <div class="space-y-4">
                        <h3 class="font-bold text-xl flex items-center gap-2">
                            <Tag class="w-5 h-5 text-primary" /> Kategori
                        </h3>
                        <div class="flex flex-col gap-2">
                            <Link 
                                :href="route('public.articles.index')"
                                class="flex items-center justify-between p-3 rounded-lg transition-colors hover:bg-gray-100 dark:hover:bg-zinc-800"
                                :class="{'bg-primary/10 text-primary font-medium': !filters.category}"
                            >
                                <span>Semua Kategori</span>
                                <ChevronRight class="w-4 h-4" />
                            </Link>
                            <Link 
                                v-for="category in categories" 
                                :key="category.id"
                                :href="route('public.articles.index', { category: category.slug })"
                                class="flex items-center justify-between p-3 rounded-lg transition-colors hover:bg-gray-100 dark:hover:bg-zinc-800"
                                :class="{'bg-primary/10 text-primary font-medium': filters.category === category.slug}"
                            >
                                <span>{{ category.name }}</span>
                                <ChevronRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    <div class="space-y-4 pt-4 border-t">
                        <h3 class="font-bold text-xl">Berita Terbaru</h3>
                        <div class="space-y-4">
                            <Link 
                                v-for="recent in recentArticles" 
                                :key="recent.id" 
                                :href="route('public.articles.show', recent.slug)"
                                class="group flex gap-4 items-start"
                            >
                                <div class="w-20 h-20 rounded-md overflow-hidden bg-gray-100 shrink-0">
                                    <img :src="getImageUrl(recent.image)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="Thumbnail">
                                </div>
                                <div>
                                    <h4 class="font-medium text-sm line-clamp-2 group-hover:text-primary transition-colors">
                                        {{ recent.title }}
                                    </h4>
                                    <span class="text-xs text-muted-foreground mt-1 block">
                                        {{ formatDate(recent.created_at) }}
                                    </span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right Content (Article Grid) -->
                <div class="lg:col-span-3">
                    <div v-if="articles.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Card 
                            v-for="article in articles.data" 
                            :key="article.id" 
                            class="group overflow-hidden hover:shadow-lg transition-all duration-300 border-border/50 bg-card"
                        >
                            <!-- Image -->
                            <div class="relative aspect-video overflow-hidden bg-gray-100">
                                <Link :href="route('public.articles.show', article.slug)">
                                    <img 
                                        :src="getImageUrl(article.image)" 
                                        :alt="article.title" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    >
                                </Link>
                                <Badge v-if="article.category" class="absolute top-3 left-3 shadow-sm">
                                    {{ article.category.name }}
                                </Badge>
                            </div>

                            <!-- Content -->
                            <CardContent class="p-5 space-y-3">
                                <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                    <span class="flex items-center gap-1">
                                        <Calendar class="w-3 h-3" /> {{ formatDate(article.created_at) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <User class="w-3 h-3" /> {{ article.user?.name || 'Admin' }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-lg leading-tight line-clamp-2 group-hover:text-primary transition-colors">
                                    <Link :href="route('public.articles.show', article.slug)">
                                        {{ article.title }}
                                    </Link>
                                </h3>
                                <div class="text-sm text-muted-foreground line-clamp-3" v-html="article.excerpt || article.content.substring(0, 100) + '...'"></div>
                            </CardContent>

                            <CardFooter class="p-5 pt-0 mt-auto">
                                <Button variant="link" class="p-0 h-auto gap-2 group/btn" as-child>
                                    <Link :href="route('public.articles.show', article.slug)">
                                        Baca Selengkapnya 
                                        <ArrowRight class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                                    </Link>
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-20 bg-gray-50 dark:bg-zinc-900/50 rounded-xl border border-dashed">
                        <h3 class="text-xl font-medium mb-2">Tidak ada artikel ditemukan</h3>
                        <p class="text-muted-foreground">Coba ubah kata kunci pencarian atau kategori Anda.</p>
                        <Button variant="outline" class="mt-4" @click="search = ''; router.visit(route('public.articles.index'))">
                            Reset Filter
                        </Button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="articles.links.length > 3" class="mt-12 flex justify-center">
                        <div class="flex flex-wrap gap-2 justify-center">
                            <template v-for="(link, key) in articles.links" :key="key">
                                <div v-if="link.url === null" 
                                    class="px-4 py-2 text-sm text-gray-400 border rounded-md cursor-not-allowed"
                                    v-html="link.label"
                                ></div>
                                <Link v-else
                                    :href="link.url"
                                    class="px-4 py-2 text-sm border rounded-md transition-colors hover:bg-gray-100"
                                    :class="{ 'bg-primary text-primary-foreground border-primary hover:bg-primary/90': link.active }"
                                    v-html="link.label"
                                ></Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
