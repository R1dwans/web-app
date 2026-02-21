<script setup>
/**
 * BlockRenderer - Renders page builder blocks for public-facing pages.
 * Supports static blocks + dynamic data blocks (articles, slider, events, facilities, prodi).
 */
import { ref, computed } from 'vue';

const props = defineProps({
    blocks: { type: Array, default: () => [] },
    dynamicData: { type: Object, default: () => ({}) },
});

const getYouTubeId = (url) => {
    if (!url) return null;
    const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    return match ? match[1] : null;
};
const getImgSrc = (p) => (!p ? '' : p.startsWith('http') ? p : `/storage/${p}`);

const formatDate = (d) => {
    if (!d) return '';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

// Accordion toggle
const openAccordion = ref({});
const toggleAccordion = (blockId, idx) => {
    const key = `${blockId}_${idx}`;
    openAccordion.value[key] = !openAccordion.value[key];
};
const isAccordionOpen = (blockId, idx) => openAccordion.value[`${blockId}_${idx}`] || false;

// Slider state
const sliderStates = ref({});
const getSliderIndex = (blockId) => sliderStates.value[blockId] || 0;
const setSliderIndex = (blockId, idx, total) => {
    sliderStates.value[blockId] = ((idx % total) + total) % total;
};

const getDynamicItems = (blockId) => props.dynamicData?.[blockId] || [];
</script>

<template>
    <div class="page-builder-output">
        <template v-for="(block, index) in blocks" :key="block.id || index">

            <!-- HERO -->
            <section v-if="block.type === 'hero'" class="relative overflow-hidden" :style="{ backgroundColor: block.data.bgColor, color: block.data.textColor, backgroundImage: block.data.bgImage ? `url(${getImgSrc(block.data.bgImage)})` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }">
                <div v-if="block.data.bgImage && block.data.overlay" class="absolute inset-0 bg-black/50"></div>
                <div class="max-w-5xl mx-auto px-6 py-20 md:py-28 text-center relative z-10">
                    <h2 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight leading-tight">{{ block.data.title }}</h2>
                    <p class="text-lg md:text-xl opacity-80 max-w-2xl mx-auto mb-8">{{ block.data.subtitle }}</p>
                    <a v-if="block.data.buttonText" :href="block.data.buttonUrl || '#'" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105">
                        {{ block.data.buttonText }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10 bg-white -translate-y-1/3 translate-x-1/3"></div>
                <div class="absolute bottom-0 left-0 w-72 h-72 rounded-full opacity-10 bg-white translate-y-1/3 -translate-x-1/3"></div>
            </section>

            <!-- TEXT -->
            <section v-else-if="block.type === 'text'" class="max-w-4xl mx-auto px-6 py-10">
                <div class="prose prose-lg dark:prose-invert max-w-none" v-html="block.data.content"></div>
            </section>

            <!-- IMAGE -->
            <section v-else-if="block.type === 'image'" class="py-10" :class="block.data.fullWidth ? '' : 'max-w-4xl mx-auto px-6'">
                <figure v-if="block.data.url">
                    <div class="overflow-hidden" :class="block.data.rounded ? 'rounded-xl' : ''">
                        <img :src="getImgSrc(block.data.url)" :alt="block.data.alt || ''" class="w-full h-auto object-cover" loading="lazy" />
                    </div>
                    <figcaption v-if="block.data.caption" class="text-center text-sm text-gray-500 dark:text-gray-400 mt-3 px-6">{{ block.data.caption }}</figcaption>
                </figure>
            </section>

            <!-- CARDS -->
            <section v-else-if="block.type === 'cards'" class="max-w-6xl mx-auto px-6 py-12">
                <h3 v-if="block.data.title" class="text-2xl font-bold text-center mb-8">{{ block.data.title }}</h3>
                <div class="grid gap-6" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 3}, 1fr)` }">
                    <div v-for="(card, ci) in block.data.items" :key="ci" class="group bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="text-4xl mb-4">{{ card.icon }}</div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ card.title }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ card.description }}</p>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section v-else-if="block.type === 'cta'" class="relative overflow-hidden" :style="{ backgroundColor: block.data.bgColor }">
                <div class="max-w-4xl mx-auto px-6 py-16 md:py-20 text-center text-white relative z-10">
                    <h3 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight">{{ block.data.title }}</h3>
                    <p class="text-lg opacity-80 max-w-2xl mx-auto mb-8">{{ block.data.description }}</p>
                    <a v-if="block.data.buttonText" :href="block.data.buttonUrl || '#'" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-gray-900 hover:bg-gray-100 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 shadow-lg">{{ block.data.buttonText }}</a>
                </div>
                <div class="absolute top-10 right-10 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute bottom-10 left-10 w-24 h-24 bg-white/10 rounded-full"></div>
            </section>

            <!-- SPACER -->
            <div v-else-if="block.type === 'spacer'" :style="{ height: (block.data.height || 40) + 'px' }"></div>

            <!-- DIVIDER -->
            <div v-else-if="block.type === 'divider'" class="py-4 flex justify-center px-6">
                <hr :style="{ borderStyle: block.data.style || 'solid', borderColor: block.data.color || '#e5e7eb', width: (block.data.width || 50) + '%' }" class="border-t-2" />
            </div>

            <!-- VIDEO -->
            <section v-else-if="block.type === 'video'" class="max-w-4xl mx-auto px-6 py-10">
                <div v-if="getYouTubeId(block.data.url)" class="aspect-video rounded-2xl overflow-hidden shadow-xl">
                    <iframe :src="`https://www.youtube.com/embed/${getYouTubeId(block.data.url)}`" class="w-full h-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <p v-if="block.data.caption" class="text-center text-sm text-gray-500 dark:text-gray-400 mt-3">{{ block.data.caption }}</p>
            </section>

            <!-- GALLERY -->
            <section v-else-if="block.type === 'gallery'" class="max-w-6xl mx-auto px-6 py-12">
                <div v-if="block.data.images?.length > 0" class="grid gap-4" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 3}, 1fr)` }">
                    <div v-for="(img, gi) in block.data.images" :key="gi" class="group relative aspect-square rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow">
                        <img v-if="img.url" :src="getImgSrc(img.url)" :alt="img.caption || ''" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                        <div v-if="img.caption" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent px-4 py-3">
                            <p class="text-white text-sm font-medium">{{ img.caption }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- COLUMNS -->
            <section v-else-if="block.type === 'columns'" class="max-w-6xl mx-auto px-6 py-10">
                <div class="grid gap-6" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 2}, 1fr)` }">
                    <div v-for="(col, ci) in block.data.items" :key="ci">
                        <!-- New: nested blocks -->
                        <template v-if="col.blocks && col.blocks.length > 0">
                            <BlockRenderer :blocks="col.blocks" :dynamicData="dynamicData" />
                        </template>
                        <!-- Legacy: raw HTML content fallback -->
                        <div v-else-if="col.content" class="prose prose-sm dark:prose-invert max-w-none" v-html="col.content"></div>
                    </div>
                </div>
            </section>

            <!-- STATS -->
            <section v-else-if="block.type === 'stats'" class="py-16 px-6" :style="{ backgroundColor: block.data.bgColor || '#f8fafc' }">
                <div class="max-w-5xl mx-auto">
                    <h3 v-if="block.data.title" class="text-2xl font-bold text-center mb-10">{{ block.data.title }}</h3>
                    <div class="grid gap-8" :style="{ gridTemplateColumns: `repeat(${block.data.items?.length || 4}, 1fr)` }">
                        <div v-for="(item, si) in block.data.items" :key="si" class="text-center">
                            <div class="text-4xl md:text-5xl font-extrabold text-indigo-600 mb-2">{{ item.value }}</div>
                            <div class="text-gray-600 dark:text-gray-400 font-medium">{{ item.label }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- TESTIMONIAL -->
            <section v-else-if="block.type === 'testimonial'" class="max-w-5xl mx-auto px-6 py-12">
                <div class="grid gap-6" :class="block.data.items?.length > 1 ? 'md:grid-cols-2' : ''">
                    <div v-for="(item, ti) in block.data.items" :key="ti" class="bg-gray-50 dark:bg-zinc-800/50 rounded-2xl p-8 relative">
                        <svg class="w-10 h-10 text-indigo-200 dark:text-indigo-800 absolute top-6 right-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        <p class="text-gray-700 dark:text-gray-300 italic text-lg leading-relaxed mb-6 relative z-10">"{{ item.quote }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 font-bold text-lg">{{ item.name?.[0] || '?' }}</div>
                            <div><p class="font-bold">{{ item.name }}</p><p class="text-sm text-gray-500">{{ item.role }}</p></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ACCORDION -->
            <section v-else-if="block.type === 'accordion'" class="max-w-3xl mx-auto px-6 py-12">
                <h3 v-if="block.data.title" class="text-2xl font-bold text-center mb-8">{{ block.data.title }}</h3>
                <div class="space-y-3">
                    <div v-for="(item, ai) in block.data.items" :key="ai" class="border border-gray-200 dark:border-zinc-700 rounded-xl overflow-hidden">
                        <button @click="toggleAccordion(block.id || index, ai)" class="w-full px-6 py-4 text-left font-medium flex items-center justify-between hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                            {{ item.question }}
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" :class="isAccordionOpen(block.id || index, ai) ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div v-show="isAccordionOpen(block.id || index, ai)" class="px-6 py-4 text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-zinc-700 bg-gray-50/50 dark:bg-zinc-800/50">{{ item.answer }}</div>
                    </div>
                </div>
            </section>

            <!-- BUTTON -->
            <section v-else-if="block.type === 'button'" class="px-6 py-8" :class="{ 'text-left': block.data.align === 'left', 'text-center': block.data.align === 'center', 'text-right': block.data.align === 'right' }">
                <a 
                    :href="block.data.url || '#'" 
                    class="inline-block px-8 py-3.5 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 hover:shadow-lg"
                    :class="{
                        'bg-indigo-600 text-white hover:bg-indigo-700': block.data.variant === 'primary',
                        'bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300': block.data.variant === 'secondary',
                        'border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50': block.data.variant === 'outline',
                        'w-full text-center': block.data.fullWidth,
                    }"
                >{{ block.data.text }}</a>
            </section>

            <!-- ========== DYNAMIC BLOCKS ========== -->

            <!-- LATEST ARTICLES -->
            <section v-else-if="block.type === 'latest_articles'" class="max-w-6xl mx-auto px-6 py-12">
                <h3 v-if="block.data.title" class="text-2xl font-bold text-center mb-8">{{ block.data.title }}</h3>
                <div v-if="getDynamicItems(block.id).length > 0" class="grid gap-6" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 3}, 1fr)` }">
                    <a v-for="article in getDynamicItems(block.id)" :key="article.id" :href="`/berita/${article.slug}`" class="group bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div v-if="block.data.show_image && article.image" class="aspect-video overflow-hidden">
                            <img :src="getImgSrc(article.image)" :alt="article.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                        </div>
                        <div class="p-5">
                            <div v-if="block.data.show_category && article.category" class="mb-2">
                                <span class="text-xs font-medium px-2.5 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full">{{ article.category.name }}</span>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 transition line-clamp-2">{{ article.title }}</h4>
                            <p v-if="block.data.show_date" class="text-xs text-gray-400">{{ formatDate(article.created_at) }}</p>
                        </div>
                    </a>
                </div>
                <div v-else class="text-center py-10 text-gray-400"><p>Belum ada artikel</p></div>
            </section>

            <!-- SLIDER MODULE -->
            <section v-else-if="block.type === 'slider_module'" class="relative overflow-hidden" :style="{ height: (block.data.height || 400) + 'px' }">
                <template v-if="getDynamicItems(block.id).length > 0">
                    <div v-for="(slide, si) in getDynamicItems(block.id)" :key="slide.id" 
                        v-show="getSliderIndex(block.id) === si"
                        class="absolute inset-0 transition-opacity duration-700"
                    >
                        <img v-if="slide.image" :src="getImgSrc(slide.image)" :alt="slide.title" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        <div v-if="block.data.show_text" class="absolute bottom-0 left-0 right-0 p-8 md:p-12 text-white">
                            <h3 class="text-2xl md:text-4xl font-extrabold mb-2 tracking-tight">{{ slide.title }}</h3>
                            <p v-if="slide.description" class="text-lg opacity-80 max-w-xl">{{ slide.description }}</p>
                            <a v-if="slide.link" :href="slide.link" class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg font-medium transition">Selengkapnya →</a>
                        </div>
                    </div>
                    <!-- Nav Buttons -->
                    <button @click="setSliderIndex(block.id, getSliderIndex(block.id) - 1, getDynamicItems(block.id).length)" class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="setSliderIndex(block.id, getSliderIndex(block.id) + 1, getDynamicItems(block.id).length)" class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 bg-white/20 hover:bg-white/40 backdrop-blur-sm rounded-full flex items-center justify-center text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <!-- Dots -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-10 flex gap-2">
                        <button v-for="(_, di) in getDynamicItems(block.id)" :key="di" @click="setSliderIndex(block.id, di, getDynamicItems(block.id).length)" class="w-2.5 h-2.5 rounded-full transition-all" :class="getSliderIndex(block.id) === di ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/70'"></button>
                    </div>
                </template>
            </section>

            <!-- EVENTS LIST -->
            <section v-else-if="block.type === 'events_list'" class="max-w-6xl mx-auto px-6 py-12">
                <h3 v-if="block.data.title" class="text-2xl font-bold text-center mb-8">{{ block.data.title }}</h3>
                <div v-if="getDynamicItems(block.id).length > 0" class="grid gap-6" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 3}, 1fr)` }">
                    <a v-for="evt in getDynamicItems(block.id)" :key="evt.id" :href="`/agenda/${evt.slug}`" class="group bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div v-if="block.data.show_image && evt.image" class="aspect-video overflow-hidden">
                            <img :src="getImgSrc(evt.image)" :alt="evt.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex flex-col items-center justify-center">
                                    <span class="text-xs font-bold text-indigo-600 leading-none">{{ new Date(evt.start_date).getDate() }}</span>
                                    <span class="text-[9px] text-indigo-400 uppercase">{{ new Date(evt.start_date).toLocaleDateString('id-ID', { month: 'short' }) }}</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-indigo-600 transition line-clamp-2">{{ evt.title }}</h4>
                                    <p v-if="evt.location" class="text-[11px] text-gray-400">📍 {{ evt.location }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div v-else class="text-center py-10 text-gray-400"><p>Belum ada agenda</p></div>
            </section>

            <!-- FACILITIES GRID -->
            <section v-else-if="block.type === 'facilities_grid'" class="max-w-6xl mx-auto px-6 py-12">
                <h3 v-if="block.data.title" class="text-2xl font-bold text-center mb-8">{{ block.data.title }}</h3>
                <div v-if="getDynamicItems(block.id).length > 0" class="grid gap-6" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 4}, 1fr)` }">
                    <a v-for="fac in getDynamicItems(block.id)" :key="fac.id" :href="`/fasilitas/${fac.slug}`" class="group bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div v-if="fac.image" class="aspect-video overflow-hidden">
                            <img :src="getImgSrc(fac.image)" :alt="fac.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" />
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-indigo-600 transition">{{ fac.title }}</h4>
                            <p v-if="block.data.show_description && fac.description" class="text-xs text-gray-500 mt-1 line-clamp-2">{{ fac.description }}</p>
                        </div>
                    </a>
                </div>
            </section>

            <!-- PROGRAM STUDIES -->
            <section v-else-if="block.type === 'program_studies'" class="max-w-6xl mx-auto px-6 py-12">
                <h3 v-if="block.data.title" class="text-2xl font-bold text-center mb-8">{{ block.data.title }}</h3>
                <div v-if="getDynamicItems(block.id).length > 0" class="grid gap-6" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 3}, 1fr)` }">
                    <a v-for="prodi in getDynamicItems(block.id)" :key="prodi.id" :href="`/prodi/${prodi.slug}`" class="group bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 rounded-2xl p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center">
                        <div class="w-14 h-14 mx-auto rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold mb-4">
                            {{ prodi.name?.[0] || '?' }}
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition mb-1">{{ prodi.name }}</h4>
                        <p v-if="block.data.show_degree && prodi.degree" class="text-xs font-medium text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-full inline-block">{{ prodi.degree }}</p>
                    </a>
                </div>
            </section>

        </template>
    </div>
</template>
