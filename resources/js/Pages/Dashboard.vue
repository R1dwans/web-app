<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    FileText, Calendar, Building2, BookOpen, Users, Download,
    Image, FileCheck, Plus, ArrowRight, TrendingUp, Clock,
    PenLine, Settings, LayoutDashboard
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    recentArticles: Array,
    upcomingEvents: Array,
    recentUsers: Array,
});

const page = usePage();
const userName = computed(() => page.props.auth.user?.name || 'Admin');

const statCards = computed(() => [
    { label: 'Artikel', value: props.stats.articles, icon: FileText, color: 'from-blue-500 to-blue-600', bg: 'bg-blue-50', text: 'text-blue-600', route: 'articles.index' },
    { label: 'Agenda', value: props.stats.events, icon: Calendar, color: 'from-emerald-500 to-emerald-600', bg: 'bg-emerald-50', text: 'text-emerald-600', route: 'events.index' },
    { label: 'Fasilitas', value: props.stats.facilities, icon: Building2, color: 'from-amber-500 to-amber-600', bg: 'bg-amber-50', text: 'text-amber-600', route: 'facilities.index' },
    { label: 'Halaman', value: props.stats.pages, icon: FileCheck, color: 'from-purple-500 to-purple-600', bg: 'bg-purple-50', text: 'text-purple-600', route: 'pages.index' },
    { label: 'Prodi', value: props.stats.program_studies, icon: BookOpen, color: 'from-rose-500 to-rose-600', bg: 'bg-rose-50', text: 'text-rose-600', route: 'program-studies.index' },
    { label: 'Pengguna', value: props.stats.users, icon: Users, color: 'from-indigo-500 to-indigo-600', bg: 'bg-indigo-50', text: 'text-indigo-600', route: 'users.index' },
    { label: 'Dokumen', value: props.stats.documents, icon: Download, color: 'from-teal-500 to-teal-600', bg: 'bg-teal-50', text: 'text-teal-600', route: 'documents.index' },
    { label: 'Slider', value: props.stats.sliders, icon: Image, color: 'from-pink-500 to-pink-600', bg: 'bg-pink-50', text: 'text-pink-600', route: 'sliders.index' },
]);

const quickActions = [
    { label: 'Tulis Artikel', icon: PenLine, route: 'articles.create', color: 'bg-blue-500 hover:bg-blue-600' },
    { label: 'Tambah Agenda', icon: Calendar, route: 'events.create', color: 'bg-emerald-500 hover:bg-emerald-600' },
    { label: 'Tambah Halaman', icon: FileCheck, route: 'pages.create', color: 'bg-purple-500 hover:bg-purple-600' },
    { label: 'Upload Dokumen', icon: Download, route: 'documents.create', color: 'bg-teal-500 hover:bg-teal-600' },
    { label: 'Tambah Fasilitas', icon: Building2, route: 'facilities.create', color: 'bg-amber-500 hover:bg-amber-600' },
    { label: 'Pengaturan', icon: Settings, route: 'settings.index', color: 'bg-gray-500 hover:bg-gray-600' },
];

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric'
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit'
    });
};

const timeAgo = (dateString) => {
    const now = new Date();
    const date = new Date(dateString);
    const diff = Math.floor((now - date) / 1000);
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`;
    return formatDate(dateString);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Welcome Banner -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 p-8 mb-8 shadow-lg">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <LayoutDashboard class="w-6 h-6 text-indigo-200" />
                            <span class="text-indigo-200 text-sm font-medium">Dashboard</span>
                        </div>
                        <h1 class="text-3xl font-bold text-white mb-2">
                            Selamat Datang, {{ userName }}! 👋
                        </h1>
                        <p class="text-indigo-200 text-sm">
                            Kelola konten dan pantau aktivitas situs Anda dari sini.
                        </p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <Link
                        v-for="stat in statCards"
                        :key="stat.label"
                        :href="route(stat.route)"
                        class="group relative overflow-hidden bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-0.5"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">{{ stat.label }}</p>
                                <p class="text-3xl font-bold text-gray-900">{{ stat.value }}</p>
                            </div>
                            <div :class="[stat.bg, 'p-2.5 rounded-xl group-hover:scale-110 transition-transform duration-300']">
                                <component :is="stat.icon" :class="[stat.text, 'w-5 h-5']" />
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity" :class="stat.color"></div>
                    </Link>
                </div>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <TrendingUp class="w-5 h-5 text-indigo-500" />
                        Aksi Cepat
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <Link
                            v-for="action in quickActions"
                            :key="action.label"
                            :href="route(action.route)"
                            :class="[action.color, 'flex items-center gap-2 px-4 py-3 text-white rounded-xl text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5']"
                        >
                            <component :is="action.icon" class="w-4 h-4 flex-shrink-0" />
                            <span class="truncate">{{ action.label }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Recent Articles -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <FileText class="w-4 h-4 text-blue-500" />
                                Artikel Terbaru
                            </h3>
                            <Link :href="route('articles.index')" class="text-xs text-indigo-600 hover:underline flex items-center gap-1">
                                Lihat semua <ArrowRight class="w-3 h-3" />
                            </Link>
                        </div>
                        <div v-if="recentArticles.length > 0" class="divide-y divide-gray-50">
                            <Link
                                v-for="article in recentArticles"
                                :key="article.id"
                                :href="route('articles.edit', article.id)"
                                class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 transition-colors group"
                            >
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                    <img v-if="article.image" :src="`/storage/${article.image}`" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <FileText class="w-4 h-4 text-gray-300" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600 transition">{{ article.title }}</p>
                                    <p class="text-xs text-gray-400">
                                        <span v-if="article.category" class="text-blue-500">{{ article.category.name }} · </span>
                                        {{ timeAgo(article.created_at) }}
                                    </p>
                                </div>
                            </Link>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-gray-400 text-sm">
                            Belum ada artikel.
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <Calendar class="w-4 h-4 text-emerald-500" />
                                Agenda Mendatang
                            </h3>
                            <Link :href="route('events.index')" class="text-xs text-indigo-600 hover:underline flex items-center gap-1">
                                Lihat semua <ArrowRight class="w-3 h-3" />
                            </Link>
                        </div>
                        <div v-if="upcomingEvents.length > 0" class="divide-y divide-gray-50">
                            <Link
                                v-for="event in upcomingEvents"
                                :key="event.id"
                                :href="route('events.edit', event.id)"
                                class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 transition-colors group"
                            >
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-50 rounded-xl flex flex-col items-center justify-center">
                                    <span class="text-sm font-bold text-emerald-600 leading-none">{{ new Date(event.start_date).getDate() }}</span>
                                    <span class="text-[10px] text-emerald-500 uppercase font-medium">{{ new Date(event.start_date).toLocaleString('id-ID', { month: 'short' }) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-indigo-600 transition">{{ event.title }}</p>
                                    <p class="text-xs text-gray-400 flex items-center gap-1">
                                        <Clock class="w-3 h-3" />
                                        {{ formatTime(event.start_date) }} WIB
                                        <span v-if="event.location"> · {{ event.location }}</span>
                                    </p>
                                </div>
                            </Link>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-gray-400 text-sm">
                            Tidak ada agenda mendatang.
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
