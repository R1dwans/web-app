<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Search, ArrowRight, FileText, Calendar, MapPin, GraduationCap, Building2, Newspaper } from 'lucide-vue-next';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Badge } from '@/Components/ui/badge';

const props = defineProps({
    results: Array,
    query: String,
    recentArticles: Array,
    categories: Array,
});

const searchQuery = ref(props.query);

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('public.search'), { q: searchQuery.value });
    }
};

const getTypeIcon = (type) => {
    switch (type) {
        case 'Berita': return Newspaper;
        case 'Halaman': return FileText;
        case 'Agenda': return Calendar;
        case 'Fasilitas': return Building2;
        case 'Program Studi': return GraduationCap;
        default: return FileText;
    }
};

const getTypeColor = (type) => {
    switch (type) {
        case 'Berita': return 'bg-blue-50 text-blue-600 ring-blue-500/10';
        case 'Halaman': return 'bg-gray-50 text-gray-600 ring-gray-500/10';
        case 'Agenda': return 'bg-purple-50 text-purple-600 ring-purple-500/10';
        case 'Fasilitas': return 'bg-green-50 text-green-600 ring-green-500/10';
        case 'Program Studi': return 'bg-indigo-50 text-indigo-600 ring-indigo-500/10';
        default: return 'bg-gray-50 text-gray-600 ring-gray-500/10';
    }
};

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
    <Head :title="`Hasil Pencarian: ${query}`" />

    <PublicLayout>
        <div class="py-12 bg-white dark:bg-zinc-950 min-h-[60vh]">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <!-- Main Content -->
                    <div class="lg:col-span-8">
                        <!-- Search Header -->
                        <div class="mb-10">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Pencarian</h1>
                            <form @submit.prevent="handleSearch" class="relative group">
                                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-indigo-600 transition" />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari berita, agenda, prodi..."
                                    class="w-full pl-12 pr-24 py-4 rounded-2xl border-gray-200 dark:border-zinc-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-lg bg-gray-50 dark:bg-zinc-900 dark:text-white"
                                />
                                <button
                                    type="submit"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold"
                                >
                                    Cari
                                </button>
                            </form>
                        </div>

                        <!-- Results Info -->
                        <div class="mb-6">
                            <p v-if="query" class="text-gray-600 dark:text-gray-400">
                                Menampilkan <span class="font-bold text-gray-900 dark:text-white">{{ results.length }}</span> hasil untuk <span class="italic">"{{ query }}"</span>
                            </p>
                            <p v-else class="text-gray-600 dark:text-gray-400">Silakan masukkan kata kunci untuk memulai pencarian.</p>
                        </div>

                        <!-- Results List -->
                        <div v-if="results.length > 0" class="space-y-4">
                            <div
                                v-for="(result, index) in results"
                                :key="index"
                                class="group bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-100 dark:border-zinc-800 hover:shadow-lg hover:border-indigo-100 dark:hover:border-indigo-900 transition duration-300"
                            >
                                <Link :href="result.url" class="flex flex-col md:flex-row md:items-center gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span
                                                :class="getTypeColor(result.type)"
                                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                            >
                                                <component :is="getTypeIcon(result.type)" class="w-3 h-3 mr-1" />
                                                {{ result.type }}
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition mb-2 line-clamp-1">
                                            {{ result.title }}
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 leading-relaxed">
                                            {{ result.description }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <ArrowRight class="w-5 h-5 text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition" />
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="query" class="text-center py-20 bg-gray-50 dark:bg-zinc-900/50 rounded-3xl border border-dashed border-gray-300 dark:border-zinc-800">
                            <div class="mb-4 inline-flex p-4 bg-gray-100 dark:bg-zinc-800 rounded-full text-gray-400">
                                <Search class="w-10 h-10" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak ditemukan hasil</h3>
                            <p class="text-gray-500 dark:text-gray-400">Coba gunakan kata kunci yang lebih umum atau periksa ejaan Anda.</p>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <aside class="lg:col-span-4 space-y-6">
                        <!-- Categories Widget -->
                        <div v-if="categories && categories.length > 0" class="bg-white dark:bg-zinc-900 rounded-xl p-6 border shadow-sm">
                            <h3 class="font-bold text-lg mb-4">Kategori</h3>
                            <div class="space-y-2">
                                <Link
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :href="route('public.articles.index') + '?category=' + cat.slug"
                                    class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 transition text-sm"
                                >
                                    <span class="font-medium">{{ cat.name }}</span>
                                    <Badge variant="secondary" class="text-xs">{{ cat.articles_count }}</Badge>
                                </Link>
                            </div>
                        </div>

                        <!-- Recent Articles Widget -->
                        <div v-if="recentArticles && recentArticles.length > 0" class="bg-white dark:bg-zinc-900 rounded-xl p-6 border shadow-sm">
                            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <Newspaper class="w-5 h-5 text-primary" />
                                Berita Terbaru
                            </h3>
                            <div class="space-y-4">
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
                        </div>
                    </aside>

                </div>
            </div>
        </div>
    </PublicLayout>
</template>
