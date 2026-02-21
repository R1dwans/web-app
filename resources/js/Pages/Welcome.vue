<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    sliders: Array,
    latestArticles: Array,
    upcomingEvents: Array,
});

const currentSlide = ref(0);
let slideInterval = null;

const nextSlide = () => {
    if (props.sliders.length > 0) {
        currentSlide.value = (currentSlide.value + 1) % props.sliders.length;
    }
};

const prevSlide = () => {
    if (props.sliders.length > 0) {
        currentSlide.value = (currentSlide.value - 1 + props.sliders.length) % props.sliders.length;
    }
};

onMounted(() => {
    if (props.sliders.length > 1) {
        slideInterval = setInterval(nextSlide, 5000);
    }
});

onUnmounted(() => {
    if (slideInterval) clearInterval(slideInterval);
});

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};
</script>

<template>
    <Head title="Welcome" />
    
    <PublicLayout :can-login="canLogin" :can-register="canRegister">
        <!-- Hero Slider -->
        <div v-if="sliders.length > 0" class="relative h-[500px] md:h-[600px] overflow-hidden bg-gray-900">
            <div 
                v-for="(slider, index) in sliders" 
                :key="slider.id"
                class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                :class="{ 'opacity-100': currentSlide === index, 'opacity-0': currentSlide !== index }"
            >
                <img :src="`/storage/${slider.image}`" class="w-full h-full object-cover opacity-60" />
                <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4">
                    <h1 v-if="slider.title" class="text-4xl md:text-6xl font-bold text-white mb-4 drop-shadow-lg animate-fade-in-up">
                        {{ slider.title }}
                    </h1>
                    <p v-if="slider.description" class="text-lg md:text-xl text-gray-200 max-w-2xl mb-8 drop-shadow-md animate-fade-in-up delay-100">
                        {{ slider.description }}
                    </p>
                    <a v-if="slider.link" :href="slider.link" class="px-8 py-3 bg-indigo-600 text-white rounded-full hover:to-indigo-700 transition transform hover:scale-105 shadow-lg animate-fade-in-up delay-200">
                        Selengkapnya
                    </a>
                </div>
            </div>
            
            <!-- Slider Controls -->
            <button @click="prevSlide" class="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-black/30 text-white rounded-full hover:bg-black/50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button @click="nextSlide" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-black/30 text-white rounded-full hover:bg-black/50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-2">
                <button 
                    v-for="(slider, index) in sliders" 
                    :key="'dot-'+slider.id"
                    @click="currentSlide = index"
                    class="w-3 h-3 rounded-full transition-colors"
                    :class="currentSlide === index ? 'bg-white' : 'bg-white/50 hover:bg-white/80'"
                ></button>
            </div>
        </div>
        
        <!-- Fallback Hero -->
        <div v-else class="relative h-[400px] bg-indigo-900 flex items-center justify-center text-center px-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Title</h1>
                <p class="text-indigo-200 text-lg">Title Description</p>
            </div>
        </div>

        <!-- Latest News -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-end mb-10">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Berita Terbaru</h2>
                        <p class="text-gray-500 mt-2">Update kegiatan dan informasi akademik terbaru</p>
                    </div>
                    <Link :href="route('public.articles.index')" class="text-indigo-600 font-medium hover:underline">Lihat Semua Berita &rarr;</Link>
                </div>
                
                <div v-if="latestArticles.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <article v-for="article in latestArticles" :key="article.id" class="flex flex-col group h-full">
                        <div class="h-48 overflow-hidden rounded-xl mb-4 bg-gray-100 relative">
                             <img v-if="article.image" :src="`/storage/${article.image}`" :alt="article.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                             <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                             </div>
                             <span v-if="article.category" class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md text-xs font-semibold text-indigo-600 shadow-sm">
                                {{ article.category.name }}
                             </span>
                        </div>
                        <div class="flex-1 flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition line-clamp-2">
                                <Link :href="route('public.articles.show', article.slug)">{{ article.title }}</Link>
                            </h3>
                            <div class="text-gray-500 text-sm line-clamp-3 mb-4 flex-1" v-html="article.excerpt || article.content?.substring(0, 100) + '...'"></div>
                            
                            <div class="mt-auto flex items-center text-sm text-gray-400 pt-4 border-t">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ formatDate(article.created_at) }}
                                </span>
                                <span class="mx-auto"></span>
                                <span class="flex items-center gap-1" v-if="article.user">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ article.user.name }}
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
                <div v-else class="text-center py-10 bg-gray-50 rounded-lg">
                    <p class="text-gray-500">Belum ada berita terbaru.</p>
                </div>
            </div>
        </section>

        <!-- Upcoming Events -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-end mb-10">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Agenda Kegiatan</h2>
                        <p class="text-gray-500 mt-2">Jangan lewatkan agenda penting fakultas</p>
                    </div>
                    <Link :href="route('public.events.index')" class="text-indigo-600 font-medium hover:underline">Lihat Semua Agenda &rarr;</Link>
                </div>

                <div v-if="upcomingEvents.length > 0" class="space-y-4">
                    <div v-for="event in upcomingEvents" :key="event.id" class="bg-white rounded-xl p-6 shadow-sm flex flex-col md:flex-row gap-6 items-center md:items-start group hover:shadow-md transition">
                        <div class="flex-shrink-0 w-full md:w-24 h-24 bg-indigo-50 rounded-lg flex flex-col items-center justify-center text-indigo-600">
                            <span class="text-2xl font-bold">{{ new Date(event.start_date).getDate() }}</span>
                            <span class="text-sm font-medium uppercase">{{ new Date(event.start_date).toLocaleString('id-ID', { month: 'short' }) }}</span>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition">{{ event.title }}</h3>
                            <div class="text-sm text-gray-500 space-y-1">
                                <div class="flex items-center justify-center md:justify-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ new Date(event.start_date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} WIB
                                </div>
                                <div class="flex items-center justify-center md:justify-start gap-2" v-if="event.location">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ event.location }}
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <!-- Link to detail if needed -->
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-10">
                    <p class="text-gray-500">Tidak ada agenda dalam waktu dekat.</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
