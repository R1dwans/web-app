<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Search as SearchIcon, X } from 'lucide-vue-next';

const page = usePage();
const menu = computed(() => page.props.menus?.primary);
const menuItems = computed(() => menu.value?.root_items?.filter(item => item.url !== '/') || []);
const s = computed(() => page.props.appSettings || {});
const appName = computed(() => s.value.site_name || 'Fikes CMS');
const logoUrl = computed(() => s.value.logo ? `/storage/${s.value.logo}` : null);

const isSearchOpen = ref(false);
const searchQuery = ref('');

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('public.search'), { q: searchQuery.value });
        isSearchOpen.value = false;
        searchQuery.value = '';
    }
};

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900 font-sans flex flex-col">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <Link :href="route('welcome')" class="flex items-center gap-2 font-bold text-2xl text-indigo-600">
                            <img v-if="logoUrl" :src="logoUrl" class="h-8 w-auto" alt="Logo" />
                            {{ appName }}
                        </Link>
                    </div>
                    
                    <!-- Dynamic Menu -->
                    <div class="hidden md:flex space-x-8">
                        <Link :href="route('welcome')" class="text-gray-700 hover:text-indigo-600 transition" :class="{ 'font-bold text-indigo-600': route().current('welcome') }">
                            Beranda
                        </Link>

                        <template v-if="menu && menuItems.length > 0">
                            <template v-for="item in menuItems" :key="item.id">
                                <!-- Dropdown for items with children -->
                                <div v-if="item.children && item.children.length > 0" class="relative group">
                                    <button class="flex items-center text-gray-700 hover:text-indigo-600 transition">
                                        {{ item.title }}
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                        <Link 
                                            v-for="child in item.children" 
                                            :key="child.id" 
                                            :href="child.url || '#'" 
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            {{ child.title }}
                                        </Link>
                                    </div>
                                </div>

                                <!-- Standard Link -->
                                <Link 
                                    v-else 
                                    :href="item.url || '#'" 
                                    class="text-gray-700 hover:text-indigo-600 transition"
                                >
                                    {{ item.title }}
                                </Link>
                            </template>
                        </template>

                        <!-- Fallback Static Menu if no dynamic menu -->
                        <template v-else>
                            <Link :href="route('public.program-studies.index')" class="text-gray-700 hover:text-indigo-600 transition">Prodi</Link>
                            <Link :href="route('public.events.index')" class="text-gray-700 hover:text-indigo-600 transition">Agenda</Link>
                            <Link :href="route('public.facilities.index')" class="text-gray-700 hover:text-indigo-600 transition">Fasilitas</Link>
                            <Link :href="route('public.documents.index')" class="text-gray-700 hover:text-indigo-600 transition">Download</Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button @click="isSearchOpen = true" class="p-2 text-gray-500 hover:text-indigo-600 transition">
                            <SearchIcon class="w-5 h-5" />
                        </button>
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</Link>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Log in</Link>
                            <Link v-if="canRegister" :href="route('register')" class="text-sm px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">Register</Link>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Search Overlay -->
            <div v-if="isSearchOpen" class="fixed inset-0 z-[100] bg-white/95 backdrop-blur-sm dark:bg-zinc-950/95 flex items-start justify-center pt-20 px-6">
                <button @click="isSearchOpen = false" class="absolute top-6 right-6 p-2 text-gray-500 hover:text-red-500 transition">
                    <X class="w-8 h-8" />
                </button>
                <div class="w-full max-w-2xl text-center">
                    <h2 class="text-3xl font-bold mb-8 dark:text-white">Pencarian Situs</h2>
                    <form @submit.prevent="handleSearch" class="relative">
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Ketik kata kunci..." 
                            class="w-full text-2xl py-4 bg-transparent border-b-2 border-gray-200 dark:border-zinc-800 focus:border-indigo-500 outline-none transition dark:text-white text-center"
                            autofocus
                        />
                        <p class="mt-4 text-gray-500 text-sm italic">Tekan Enter untuk mencari</p>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12 border-t border-gray-800 mt-auto">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ appName }}</h3>
                    <p class="text-gray-400 text-sm" style="white-space: pre-line;">{{ s.site_description || 'Sistem Informasi Fakultas Ilmu Kesehatan.' }}</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><Link :href="route('welcome')" class="hover:text-white">Beranda</Link></li>
                        <li><Link :href="route('public.program-studies.index')" class="hover:text-white">Program Studi</Link></li>
                        <li><Link :href="route('public.events.index')" class="hover:text-white">Agenda</Link></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Kontak</h4>
                    <p class="text-sm text-gray-400" style="white-space: pre-line;">{{ s.contact_address || '' }}</p>
                    <p v-if="s.contact_email" class="text-sm text-gray-400 mt-2">Email: {{ s.contact_email }}</p>
                    <p v-if="s.contact_phone" class="text-sm text-gray-400 mt-1">Telp: {{ s.contact_phone }}</p>
                    <div v-if="s.facebook_url || s.instagram_url || s.youtube_url" class="flex gap-3 mt-4">
                        <a v-if="s.facebook_url" :href="s.facebook_url" target="_blank" class="text-gray-400 hover:text-white transition">Facebook</a>
                        <a v-if="s.instagram_url" :href="s.instagram_url" target="_blank" class="text-gray-400 hover:text-white transition">Instagram</a>
                        <a v-if="s.youtube_url" :href="s.youtube_url" target="_blank" class="text-gray-400 hover:text-white transition">YouTube</a>
                    </div>
                </div>
             </div>
             <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm text-gray-500">
                {{ s.footer_text || `© ${new Date().getFullYear()} ${appName}. All rights reserved.` }}
                <span class="ml-2 text-xs">Laravel v{{ $page.props.laravelVersion }} (PHP v{{ $page.props.phpVersion }})</span>
             </div>
        </footer>
    </div>
</template>
