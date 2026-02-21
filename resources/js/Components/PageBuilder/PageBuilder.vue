<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import draggable from 'vuedraggable';
import MediaPicker from './MediaPicker.vue';
import { 
    Type, Image, LayoutGrid, MousePointerClick, 
    Minus, Play, Images, PanelTop, Columns3, 
    BarChart3, Quote, ChevronRight, Square,
    Plus, GripVertical, Trash2, ChevronUp, ChevronDown, Copy, Settings,
    X, Eye, Monitor, Tablet, Smartphone, Maximize2, Minimize2,
    Undo2, Redo2, Save, Layers
} from 'lucide-vue-next';
import { Newspaper, SlidersHorizontal, CalendarDays, Building2, GraduationCap } from 'lucide-vue-next';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    onSave: { type: Function, default: null },
    pageTitle: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue', 'close', 'save']);

const blocks = ref(JSON.parse(JSON.stringify(props.modelValue || [])));
const selectedBlockId = ref(null);
const isFullscreen = ref(false);
const previewMode = ref('desktop'); // desktop, tablet, mobile
const showPreview = ref(false);
const sidebarTab = ref('blocks'); // blocks, settings
const undoStack = ref([]);
const redoStack = ref([]);
const showMediaPicker = ref(false);
const mediaPickerMultiple = ref(false);
const mediaPickerCallback = ref(null);

watch(() => props.modelValue, (val) => {
    if (JSON.stringify(val) !== JSON.stringify(blocks.value)) {
        blocks.value = JSON.parse(JSON.stringify(val || []));
    }
}, { deep: true });

watch(blocks, (val) => {
    emit('update:modelValue', JSON.parse(JSON.stringify(val)));
}, { deep: true });

// Undo/Redo
const saveState = () => {
    undoStack.value.push(JSON.stringify(blocks.value));
    if (undoStack.value.length > 30) undoStack.value.shift();
    redoStack.value = [];
};
const undo = () => {
    if (undoStack.value.length === 0) return;
    redoStack.value.push(JSON.stringify(blocks.value));
    blocks.value = JSON.parse(undoStack.value.pop());
};
const redo = () => {
    if (redoStack.value.length === 0) return;
    undoStack.value.push(JSON.stringify(blocks.value));
    blocks.value = JSON.parse(redoStack.value.pop());
};

// Keyboard shortcuts
const handleKeydown = (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); undo(); }
    if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) { e.preventDefault(); redo(); }
    if (e.key === 'Delete' && selectedBlockId.value) {
        const idx = blocks.value.findIndex(b => b.id === selectedBlockId.value);
        if (idx !== -1 && document.activeElement?.tagName !== 'INPUT' && document.activeElement?.tagName !== 'TEXTAREA' && document.activeElement?.tagName !== 'SELECT') {
            saveState(); removeBlock(idx);
        }
    }
};
onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));

// Block types
const blockCategories = [
    { label: 'Layout', items: [
        { type: 'hero', label: 'Hero Banner', icon: PanelTop, color: 'bg-purple-500', desc: 'Banner utama' },
        { type: 'columns', label: 'Kolom', icon: Columns3, color: 'bg-indigo-500', desc: '2-4 kolom konten' },
        { type: 'spacer', label: 'Spacer', icon: Minus, color: 'bg-gray-400', desc: 'Jarak vertikal' },
        { type: 'divider', label: 'Divider', icon: Minus, color: 'bg-gray-500', desc: 'Garis pemisah' },
    ]},
    { label: 'Konten', items: [
        { type: 'text', label: 'Teks', icon: Type, color: 'bg-blue-500', desc: 'Paragraf & heading' },
        { type: 'image', label: 'Gambar', icon: Image, color: 'bg-green-500', desc: 'Gambar + caption' },
        { type: 'video', label: 'Video', icon: Play, color: 'bg-red-500', desc: 'Embed YouTube' },
        { type: 'gallery', label: 'Galeri', icon: Images, color: 'bg-teal-500', desc: 'Grid gambar' },
        { type: 'button', label: 'Tombol', icon: Square, color: 'bg-orange-500', desc: 'Link tombol' },
    ]},
    { label: 'Komponen', items: [
        { type: 'cards', label: 'Cards', icon: LayoutGrid, color: 'bg-amber-500', desc: 'Grid kartu fitur' },
        { type: 'stats', label: 'Statistik', icon: BarChart3, color: 'bg-cyan-500', desc: 'Angka & counter' },
        { type: 'testimonial', label: 'Testimonial', icon: Quote, color: 'bg-pink-500', desc: 'Kutipan & review' },
        { type: 'cta', label: 'CTA', icon: MousePointerClick, color: 'bg-rose-500', desc: 'Call to action' },
        { type: 'accordion', label: 'Accordion', icon: ChevronRight, color: 'bg-violet-500', desc: 'FAQ / dropdown' },
    ]},
    { label: 'Data Dinamis', items: [
        { type: 'latest_articles', label: 'Berita', icon: Newspaper, color: 'bg-emerald-600', desc: 'Artikel terbaru' },
        { type: 'slider_module', label: 'Slider', icon: SlidersHorizontal, color: 'bg-sky-600', desc: 'Dari modul Slider' },
        { type: 'events_list', label: 'Agenda', icon: CalendarDays, color: 'bg-orange-600', desc: 'Event mendatang' },
        { type: 'facilities_grid', label: 'Fasilitas', icon: Building2, color: 'bg-stone-600', desc: 'Daftar fasilitas' },
        { type: 'program_studies', label: 'Prodi', icon: GraduationCap, color: 'bg-fuchsia-600', desc: 'Program studi' },
    ]},
];

const allBlockTypes = computed(() => blockCategories.flatMap(c => c.items));

const defaultBlockData = {
    hero: { title: 'Judul Hero', subtitle: 'Deskripsi singkat di sini', buttonText: 'Selengkapnya', buttonUrl: '#', bgColor: '#4f46e5', textColor: '#ffffff', bgImage: '', overlay: true },
    text: { content: '<p>Tulis konten Anda di sini...</p>', maxWidth: 'prose' },
    image: { url: '', caption: '', alt: '', fullWidth: false, rounded: true },
    cards: { columns: 3, title: '', items: [
        { title: 'Kartu 1', description: 'Deskripsi kartu pertama', icon: '📌' },
        { title: 'Kartu 2', description: 'Deskripsi kartu kedua', icon: '🎯' },
        { title: 'Kartu 3', description: 'Deskripsi kartu ketiga', icon: '🚀' },
    ]},
    cta: { title: 'Siap untuk memulai?', description: 'Hubungi kami sekarang untuk informasi lebih lanjut.', buttonText: 'Hubungi Kami', buttonUrl: '#', bgColor: '#4f46e5' },
    spacer: { height: 60 },
    divider: { style: 'solid', width: '50', color: '#e5e7eb' },
    video: { url: '', caption: '' },
    gallery: { images: [], columns: 3 },
    columns: { columns: 2, gap: 6, items: [{ blocks: [] }, { blocks: [] }] },
    stats: { title: '', items: [
        { value: '1000+', label: 'Mahasiswa' },
        { value: '50+', label: 'Dosen' },
        { value: '10', label: 'Program Studi' },
        { value: '95%', label: 'Kepuasan' },
    ], bgColor: '#f8fafc' },
    testimonial: { items: [
        { quote: 'Pengalaman belajar yang luar biasa dan inspiratif.', name: 'John Doe', role: 'Mahasiswa', avatar: '' },
    ]},
    accordion: { title: 'FAQ', items: [
        { question: 'Pertanyaan pertama?', answer: 'Jawaban pertama.' },
        { question: 'Pertanyaan kedua?', answer: 'Jawaban kedua.' },
    ]},
    button: { text: 'Klik di sini', url: '#', variant: 'primary', size: 'md', align: 'center', fullWidth: false },
    // Dynamic blocks
    latest_articles: { limit: 6, columns: 3, category_id: '', title: 'Berita Terbaru', show_image: true, show_date: true, show_category: true },
    slider_module: { autoplay: true, interval: 5000, show_text: true, height: 400 },
    events_list: { limit: 6, columns: 3, title: 'Agenda Mendatang', show_upcoming_only: true, show_image: true },
    facilities_grid: { limit: 8, columns: 4, title: 'Fasilitas Kami', show_description: true },
    program_studies: { title: 'Program Studi', columns: 3, show_degree: true },
};

let blockCounter = Date.now();

const addBlock = (type, insertIndex = -1) => {
    saveState();
    const newBlock = {
        id: `block_${blockCounter++}`,
        type,
        data: JSON.parse(JSON.stringify(defaultBlockData[type])),
    };
    if (insertIndex >= 0) {
        blocks.value.splice(insertIndex, 0, newBlock);
    } else {
        blocks.value.push(newBlock);
    }
    selectedBlockId.value = newBlock.id;
    sidebarTab.value = 'settings';
};

const removeBlock = (index) => {
    saveState();
    if (selectedBlockId.value === blocks.value[index]?.id) selectedBlockId.value = null;
    blocks.value.splice(index, 1);
    sidebarTab.value = 'blocks';
};
const duplicateBlock = (index) => {
    saveState();
    const original = blocks.value[index];
    const clone = { id: `block_${blockCounter++}`, type: original.type, data: JSON.parse(JSON.stringify(original.data)) };
    blocks.value.splice(index + 1, 0, clone);
    selectedBlockId.value = clone.id;
};
const moveBlock = (index, direction) => {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= blocks.value.length) return;
    saveState();
    const [item] = blocks.value.splice(index, 1);
    blocks.value.splice(newIndex, 0, item);
};

const selectedBlock = computed(() => blocks.value.find(b => b.id === selectedBlockId.value));
const selectedBlockIndex = computed(() => blocks.value.findIndex(b => b.id === selectedBlockId.value));
const getBlockTypeInfo = (type) => allBlockTypes.value.find(b => b.type === type);

// Helpers
const addCard = (block) => { saveState(); block.data.items.push({ title: 'Kartu Baru', description: 'Deskripsi', icon: '⭐' }); };
const removeCard = (block, idx) => { saveState(); block.data.items.splice(idx, 1); };
const addGalleryImage = (block) => { saveState(); block.data.images.push({ url: '', caption: '' }); };
const removeGalleryImage = (block, idx) => { saveState(); block.data.images.splice(idx, 1); };
const addStatItem = (block) => { saveState(); block.data.items.push({ value: '0', label: 'Label' }); };
const removeStatItem = (block, idx) => { saveState(); block.data.items.splice(idx, 1); };
const addTestimonial = (block) => { saveState(); block.data.items.push({ quote: 'Testimonial baru...', name: 'Nama', role: 'Jabatan', avatar: '' }); };
const removeTestimonial = (block, idx) => { saveState(); block.data.items.splice(idx, 1); };
const addAccordionItem = (block) => { saveState(); block.data.items.push({ question: 'Pertanyaan baru?', answer: 'Jawaban.' }); };
const removeAccordionItem = (block, idx) => { saveState(); block.data.items.splice(idx, 1); };
const addColumn = (block) => { if (block.data.items.length < 4) { saveState(); block.data.columns++; block.data.items.push({ blocks: [] }); }};
const removeColumn = (block, idx) => { if (block.data.items.length > 1) { saveState(); block.data.columns--; block.data.items.splice(idx, 1); }};

// Nested block helpers for columns
const addNestedBlock = (column, type) => {
    saveState();
    if (!column.blocks) column.blocks = [];
    const data = JSON.parse(JSON.stringify(defaultBlockData[type] || {}));
    column.blocks.push({ id: 'nb_' + blockCounter++, type, data });
};
const removeNestedBlock = (column, idx) => {
    saveState();
    column.blocks.splice(idx, 1);
};
const moveNestedBlock = (column, from, to) => {
    if (to < 0 || to >= column.blocks.length) return;
    saveState();
    const item = column.blocks.splice(from, 1)[0];
    column.blocks.splice(to, 0, item);
};

// Allowed nested block types (no columns-in-columns, no dynamic)
const nestedBlockTypes = computed(() => allBlockTypes.value.filter(b => !['columns', 'hero', 'latest_articles', 'slider_module', 'events_list', 'facilities_grid', 'program_studies'].includes(b.type)));

const getYouTubeId = (url) => {
    if (!url) return null;
    const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    return match ? match[1] : null;
};
const getImgSrc = (p) => p ? (p.startsWith('http') ? p : `/storage/${p}`) : '';

const toggleFullscreen = () => { isFullscreen.value = !isFullscreen.value; };
const handleSave = () => { emit('save'); };
const previewWidths = { desktop: '100%', tablet: '768px', mobile: '375px' };
const hoverInsertIndex = ref(-1);

// Media picker helpers
const openMediaPicker = (multiple, callback) => {
    mediaPickerMultiple.value = multiple;
    mediaPickerCallback.value = callback;
    showMediaPicker.value = true;
};
const onMediaSelected = (result) => {
    if (mediaPickerCallback.value) {
        saveState();
        mediaPickerCallback.value(result);
        mediaPickerCallback.value = null;
    }
};

// Input class helper
const ic = 'w-full px-3 py-2 border border-gray-200 dark:border-zinc-600 rounded-lg text-sm bg-white dark:bg-zinc-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition';
const ic2 = 'w-full px-2 py-1.5 border border-gray-200 dark:border-zinc-600 rounded text-sm bg-white dark:bg-zinc-800 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition';
</script>

<template>
    <div 
        class="page-builder-advanced flex flex-col bg-gray-100 dark:bg-zinc-950 transition-all"
        :class="isFullscreen ? 'fixed inset-0 z-[9999] overflow-hidden' : 'min-h-[700px] rounded-xl border border-gray-200 dark:border-zinc-700'"
        :style="isFullscreen ? { height: '100vh' } : {}"
    >
        <!-- Top Toolbar -->
        <div class="flex items-center justify-between px-4 py-2 bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-700 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-zinc-800 rounded-lg p-0.5">
                    <button @click="sidebarTab = 'blocks'" class="px-3 py-1.5 rounded-md text-xs font-medium transition-all" :class="sidebarTab === 'blocks' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-500 hover:text-gray-700'">
                        <Layers class="w-3.5 h-3.5 inline mr-1" />Blocks
                    </button>
                    <button @click="sidebarTab = 'settings'" :disabled="!selectedBlock" class="px-3 py-1.5 rounded-md text-xs font-medium transition-all disabled:opacity-30" :class="sidebarTab === 'settings' && selectedBlock ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-500 hover:text-gray-700'">
                        <Settings class="w-3.5 h-3.5 inline mr-1" />Settings
                    </button>
                </div>
                <div class="h-5 w-px bg-gray-200 dark:bg-zinc-700"></div>
                <button @click="undo" :disabled="undoStack.length === 0" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 disabled:opacity-30 text-gray-500" title="Undo (Ctrl+Z)"><Undo2 class="w-4 h-4" /></button>
                <button @click="redo" :disabled="redoStack.length === 0" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 disabled:opacity-30 text-gray-500" title="Redo (Ctrl+Y)"><Redo2 class="w-4 h-4" /></button>
                <span class="text-xs text-muted-foreground">{{ blocks.length }} block{{ blocks.length !== 1 ? 's' : '' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <!-- Responsive Preview -->
                <div class="flex items-center gap-0.5 bg-gray-100 dark:bg-zinc-800 rounded-lg p-0.5">
                    <button @click="previewMode = 'desktop'" class="p-1.5 rounded-md transition" :class="previewMode === 'desktop' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-400 hover:text-gray-600'"><Monitor class="w-3.5 h-3.5" /></button>
                    <button @click="previewMode = 'tablet'" class="p-1.5 rounded-md transition" :class="previewMode === 'tablet' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-400 hover:text-gray-600'"><Tablet class="w-3.5 h-3.5" /></button>
                    <button @click="previewMode = 'mobile'" class="p-1.5 rounded-md transition" :class="previewMode === 'mobile' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-400 hover:text-gray-600'"><Smartphone class="w-3.5 h-3.5" /></button>
                </div>
                <div class="h-5 w-px bg-gray-200 dark:bg-zinc-700"></div>
                <button @click="showPreview = !showPreview" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-500" :class="showPreview ? 'text-primary bg-primary/10' : ''" title="Preview"><Eye class="w-4 h-4" /></button>
                <button @click="toggleFullscreen" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-500" :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'">
                    <Minimize2 v-if="isFullscreen" class="w-4 h-4" /><Maximize2 v-else class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Main Area -->
        <div class="flex flex-1 h-0">
            <!-- Left Panel (Block Palette / Settings) -->
            <div class="w-64 shrink-0 bg-white dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-700 overflow-y-auto">
                <!-- Blocks Panel -->
                <div v-if="sidebarTab === 'blocks' || !selectedBlock" class="p-3 space-y-4">
                    <div v-for="cat in blockCategories" :key="cat.label">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">{{ cat.label }}</p>
                        <div class="grid grid-cols-2 gap-1.5">
                            <button
                                v-for="bt in cat.items"
                                :key="bt.type"
                                @click="addBlock(bt.type)"
                                class="flex flex-col items-center gap-1 px-2 py-2.5 rounded-lg border border-gray-100 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 hover:border-primary hover:bg-primary/5 hover:shadow-sm transition-all text-center group"
                            >
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" :class="bt.color">
                                    <component :is="bt.icon" class="w-4 h-4" />
                                </div>
                                <span class="text-[11px] font-medium text-gray-600 dark:text-gray-300 group-hover:text-primary transition">{{ bt.label }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Settings Panel -->
                <div v-else-if="sidebarTab === 'settings' && selectedBlock" class="p-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded flex items-center justify-center text-white" :class="getBlockTypeInfo(selectedBlock.type)?.color">
                                <component :is="getBlockTypeInfo(selectedBlock.type)?.icon" class="w-3 h-3" />
                            </div>
                            <span class="text-sm font-semibold">{{ getBlockTypeInfo(selectedBlock.type)?.label }}</span>
                        </div>
                        <button @click="selectedBlockId = null; sidebarTab = 'blocks'" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-zinc-800 text-gray-400"><X class="w-4 h-4" /></button>
                    </div>

                    <div class="space-y-3">
                        <!-- HERO Settings -->
                        <template v-if="selectedBlock.type === 'hero'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Judul</label><input v-model="selectedBlock.data.title" :class="ic" @focus="saveState" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Subjudul</label><input v-model="selectedBlock.data.subtitle" :class="ic" @focus="saveState" /></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Teks Tombol</label><input v-model="selectedBlock.data.buttonText" :class="ic" /></div>
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">URL Tombol</label><input v-model="selectedBlock.data.buttonUrl" :class="ic" /></div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-500 mb-1">Background Gambar</label>
                                <div class="flex gap-1.5">
                                    <input v-model="selectedBlock.data.bgImage" :class="ic" placeholder="URL gambar (opsional)" class="flex-1" />
                                    <button @click="openMediaPicker(false, (img) => { selectedBlock.data.bgImage = img.url })" type="button" class="px-3 py-2 bg-primary/10 text-primary rounded-lg text-xs font-medium hover:bg-primary/20 transition shrink-0">Pilih</button>
                                </div>
                                <div v-if="selectedBlock.data.bgImage" class="mt-1.5 relative w-full h-20 rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800">
                                    <img :src="getImgSrc(selectedBlock.data.bgImage)" class="w-full h-full object-cover" />
                                    <button @click="selectedBlock.data.bgImage = ''" class="absolute top-1 right-1 p-0.5 bg-red-500 text-white rounded-full"><X class="w-3 h-3" /></button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Warna BG</label><div class="flex gap-1.5"><input type="color" v-model="selectedBlock.data.bgColor" class="w-8 h-8 rounded border cursor-pointer shrink-0" /><input v-model="selectedBlock.data.bgColor" :class="ic" class="font-mono !text-xs" /></div></div>
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Warna Teks</label><div class="flex gap-1.5"><input type="color" v-model="selectedBlock.data.textColor" class="w-8 h-8 rounded border cursor-pointer shrink-0" /><input v-model="selectedBlock.data.textColor" :class="ic" class="font-mono !text-xs" /></div></div>
                            </div>
                            <div class="flex items-center gap-2"><input type="checkbox" v-model="selectedBlock.data.overlay" class="rounded" /><label class="text-xs text-gray-500">Overlay gelap</label></div>
                        </template>

                        <!-- TEXT Settings -->
                        <template v-else-if="selectedBlock.type === 'text'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Konten (HTML)</label>
                            <textarea v-model="selectedBlock.data.content" rows="10" :class="ic" class="font-mono !text-xs" @focus="saveState"></textarea></div>
                        </template>

                        <!-- IMAGE Settings -->
                        <template v-else-if="selectedBlock.type === 'image'">
                            <div>
                                <label class="block text-[11px] font-medium text-gray-500 mb-1">Gambar</label>
                                <div v-if="selectedBlock.data.url" class="relative w-full aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-zinc-800 mb-2">
                                    <img :src="getImgSrc(selectedBlock.data.url)" class="w-full h-full object-cover" />
                                    <button @click="saveState(); selectedBlock.data.url = ''" class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full shadow"><X class="w-3 h-3" /></button>
                                </div>
                                <div class="flex gap-1.5">
                                    <input v-model="selectedBlock.data.url" :class="ic" placeholder="URL atau pilih gambar" class="flex-1" @focus="saveState" />
                                    <button @click="openMediaPicker(false, (img) => { selectedBlock.data.url = img.url })" type="button" class="px-3 py-2 bg-primary/10 text-primary rounded-lg text-xs font-medium hover:bg-primary/20 transition shrink-0">Pilih</button>
                                </div>
                            </div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Alt Text</label><input v-model="selectedBlock.data.alt" :class="ic" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Caption</label><input v-model="selectedBlock.data.caption" :class="ic" /></div>
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.fullWidth" class="rounded" /> Full Width</label>
                                <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.rounded" class="rounded" /> Rounded</label>
                            </div>
                        </template>

                        <!-- CARDS Settings -->
                        <template v-else-if="selectedBlock.type === 'cards'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Judul Seksi</label><input v-model="selectedBlock.data.title" :class="ic" placeholder="Opsional" @focus="saveState" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Kolom</label>
                            <select v-model.number="selectedBlock.data.columns" :class="ic"><option :value="2">2</option><option :value="3">3</option><option :value="4">4</option></select></div>
                            <div v-for="(card, ci) in selectedBlock.data.items" :key="ci" class="border rounded-lg p-2 bg-gray-50 dark:bg-zinc-800 space-y-1.5">
                                <div class="flex items-center justify-between"><span class="text-[10px] font-bold text-gray-400">{{ ci + 1 }}</span><button @click="removeCard(selectedBlock, ci)" class="text-red-400 hover:text-red-600"><Trash2 class="w-3 h-3" /></button></div>
                                <div class="grid grid-cols-[50px_1fr] gap-1.5"><input v-model="card.icon" :class="ic2" class="text-center" /><input v-model="card.title" :class="ic2" placeholder="Judul" /></div>
                                <input v-model="card.description" :class="ic2" placeholder="Deskripsi" />
                            </div>
                            <button @click="addCard(selectedBlock)" class="w-full py-1.5 border-2 border-dashed rounded-lg text-xs text-gray-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-1"><Plus class="w-3 h-3" /> Tambah Kartu</button>
                        </template>

                        <!-- CTA Settings -->
                        <template v-else-if="selectedBlock.type === 'cta'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Judul</label><input v-model="selectedBlock.data.title" :class="ic" @focus="saveState" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Deskripsi</label><textarea v-model="selectedBlock.data.description" rows="2" :class="ic"></textarea></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Teks Tombol</label><input v-model="selectedBlock.data.buttonText" :class="ic" /></div>
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">URL</label><input v-model="selectedBlock.data.buttonUrl" :class="ic" /></div>
                            </div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Warna BG</label><div class="flex gap-1.5"><input type="color" v-model="selectedBlock.data.bgColor" class="w-8 h-8 rounded border cursor-pointer shrink-0" /><input v-model="selectedBlock.data.bgColor" :class="ic" class="font-mono !text-xs" /></div></div>
                        </template>

                        <!-- SPACER Settings -->
                        <template v-else-if="selectedBlock.type === 'spacer'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Tinggi: {{ selectedBlock.data.height }}px</label>
                            <input type="range" v-model.number="selectedBlock.data.height" min="10" max="200" class="w-full" @mousedown="saveState" /></div>
                        </template>

                        <!-- DIVIDER Settings -->
                        <template v-else-if="selectedBlock.type === 'divider'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Gaya</label>
                            <select v-model="selectedBlock.data.style" :class="ic"><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option></select></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Lebar (%)</label><input type="range" v-model.number="selectedBlock.data.width" min="10" max="100" class="w-full" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Warna</label><div class="flex gap-1.5"><input type="color" v-model="selectedBlock.data.color" class="w-8 h-8 rounded border cursor-pointer shrink-0" /><input v-model="selectedBlock.data.color" :class="ic" class="font-mono !text-xs" /></div></div>
                        </template>

                        <!-- VIDEO Settings -->
                        <template v-else-if="selectedBlock.type === 'video'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">YouTube URL</label><input v-model="selectedBlock.data.url" :class="ic" placeholder="https://youtube.com/watch?v=..." @focus="saveState" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Caption</label><input v-model="selectedBlock.data.caption" :class="ic" /></div>
                        </template>

                        <!-- GALLERY Settings -->
                        <template v-else-if="selectedBlock.type === 'gallery'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Kolom</label>
                            <select v-model.number="selectedBlock.data.columns" :class="ic"><option :value="2">2</option><option :value="3">3</option><option :value="4">4</option></select></div>
                            <div v-for="(img, gi) in selectedBlock.data.images" :key="gi" class="flex gap-1.5 items-center">
                                <div class="w-10 h-10 rounded bg-gray-100 dark:bg-zinc-700 overflow-hidden shrink-0 cursor-pointer" @click="openMediaPicker(false, (m) => { img.url = m.url })">
                                    <img v-if="img.url" :src="getImgSrc(img.url)" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center"><Image class="w-4 h-4 text-gray-300" /></div>
                                </div>
                                <input v-model="img.url" :class="ic2" placeholder="URL" class="flex-1" />
                                <button @click="openMediaPicker(false, (m) => { img.url = m.url })" type="button" class="p-1.5 bg-primary/10 text-primary rounded text-xs hover:bg-primary/20 transition shrink-0" title="Pilih gambar"><Image class="w-3 h-3" /></button>
                                <button @click="removeGalleryImage(selectedBlock, gi)" class="text-red-400 hover:text-red-600 p-1"><Trash2 class="w-3 h-3" /></button>
                            </div>
                            <div class="flex gap-1.5">
                                <button @click="addGalleryImage(selectedBlock)" class="flex-1 py-1.5 border-2 border-dashed rounded-lg text-xs text-gray-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-1"><Plus class="w-3 h-3" /> Manual</button>
                                <button @click="openMediaPicker(true, (imgs) => { saveState(); imgs.forEach(m => selectedBlock.data.images.push({ url: m.url, caption: '' })) })" type="button" class="flex-1 py-1.5 bg-primary/10 text-primary border-2 border-primary/20 rounded-lg text-xs font-medium hover:bg-primary/20 transition flex items-center justify-center gap-1"><Image class="w-3 h-3" /> Pilih Gambar</button>
                            </div>
                        </template>

                        <!-- COLUMNS Settings -->
                        <template v-else-if="selectedBlock.type === 'columns'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Jumlah Kolom</label>
                            <select v-model.number="selectedBlock.data.columns" :class="ic"><option :value="2">2</option><option :value="3">3</option><option :value="4">4</option></select></div>
                            <div v-for="(col, ci) in selectedBlock.data.items" :key="ci" class="border rounded-lg p-2 bg-gray-50 dark:bg-zinc-800 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-gray-400">Kolom {{ ci + 1 }} ({{ col.blocks?.length || 0 }} block)</span>
                                    <button v-if="selectedBlock.data.items.length > 1" @click="removeColumn(selectedBlock, ci)" class="text-red-400 hover:text-red-600"><Trash2 class="w-3 h-3" /></button>
                                </div>
                                <!-- Nested blocks list -->
                                <div v-for="(nb, nbi) in (col.blocks || [])" :key="nb.id" class="flex items-center gap-1.5 bg-white dark:bg-zinc-900 rounded-md px-2 py-1.5 border text-xs">
                                    <component :is="getBlockTypeInfo(nb.type)?.icon" class="w-3 h-3 text-gray-400 shrink-0" />
                                    <span class="flex-1 truncate text-gray-600 dark:text-gray-300">{{ getBlockTypeInfo(nb.type)?.label }}</span>
                                    <button @click="moveNestedBlock(col, nbi, nbi - 1)" class="text-gray-300 hover:text-gray-600 p-0.5" :disabled="nbi === 0"><ChevronUp class="w-3 h-3" /></button>
                                    <button @click="moveNestedBlock(col, nbi, nbi + 1)" class="text-gray-300 hover:text-gray-600 p-0.5" :disabled="nbi >= col.blocks.length - 1"><ChevronDown class="w-3 h-3" /></button>
                                    <button @click="removeNestedBlock(col, nbi)" class="text-red-300 hover:text-red-500 p-0.5"><X class="w-3 h-3" /></button>
                                </div>
                                <!-- Add block to column -->
                                <div class="relative">
                                    <select @change="(e) => { if (e.target.value) { addNestedBlock(col, e.target.value); e.target.value = ''; } }" class="w-full px-2 py-1.5 border border-dashed border-gray-300 dark:border-zinc-600 rounded-md text-xs text-gray-400 bg-white dark:bg-zinc-900 cursor-pointer">
                                        <option value="">+ Tambah block...</option>
                                        <option v-for="bt in nestedBlockTypes" :key="bt.type" :value="bt.type">{{ bt.label }}</option>
                                    </select>
                                </div>
                            </div>
                            <button v-if="selectedBlock.data.items.length < 4" @click="addColumn(selectedBlock)" class="w-full py-1.5 border-2 border-dashed rounded-lg text-xs text-gray-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-1"><Plus class="w-3 h-3" /> Tambah Kolom</button>
                        </template>

                        <!-- STATS Settings -->
                        <template v-else-if="selectedBlock.type === 'stats'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Judul Seksi</label><input v-model="selectedBlock.data.title" :class="ic" placeholder="Opsional" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Warna BG</label><div class="flex gap-1.5"><input type="color" v-model="selectedBlock.data.bgColor" class="w-8 h-8 rounded border cursor-pointer shrink-0" /><input v-model="selectedBlock.data.bgColor" :class="ic" class="font-mono !text-xs" /></div></div>
                            <div v-for="(item, si) in selectedBlock.data.items" :key="si" class="flex gap-1.5 items-center">
                                <input v-model="item.value" :class="ic2" class="w-16 text-center font-bold" />
                                <input v-model="item.label" :class="ic2" class="flex-1" />
                                <button @click="removeStatItem(selectedBlock, si)" class="text-red-400 hover:text-red-600 p-1"><Trash2 class="w-3 h-3" /></button>
                            </div>
                            <button @click="addStatItem(selectedBlock)" class="w-full py-1.5 border-2 border-dashed rounded-lg text-xs text-gray-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-1"><Plus class="w-3 h-3" /> Tambah Item</button>
                        </template>

                        <!-- TESTIMONIAL Settings -->
                        <template v-else-if="selectedBlock.type === 'testimonial'">
                            <div v-for="(item, ti) in selectedBlock.data.items" :key="ti" class="border rounded-lg p-2 bg-gray-50 dark:bg-zinc-800 space-y-1.5">
                                <div class="flex items-center justify-between"><span class="text-[10px] font-bold text-gray-400">{{ ti + 1 }}</span><button @click="removeTestimonial(selectedBlock, ti)" class="text-red-400 hover:text-red-600"><Trash2 class="w-3 h-3" /></button></div>
                                <textarea v-model="item.quote" rows="2" :class="ic2" placeholder="Kutipan" @focus="saveState"></textarea>
                                <div class="grid grid-cols-2 gap-1.5"><input v-model="item.name" :class="ic2" placeholder="Nama" /><input v-model="item.role" :class="ic2" placeholder="Jabatan" /></div>
                            </div>
                            <button @click="addTestimonial(selectedBlock)" class="w-full py-1.5 border-2 border-dashed rounded-lg text-xs text-gray-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-1"><Plus class="w-3 h-3" /> Tambah</button>
                        </template>

                        <!-- ACCORDION Settings -->
                        <template v-else-if="selectedBlock.type === 'accordion'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Judul</label><input v-model="selectedBlock.data.title" :class="ic" @focus="saveState" /></div>
                            <div v-for="(item, ai) in selectedBlock.data.items" :key="ai" class="border rounded-lg p-2 bg-gray-50 dark:bg-zinc-800 space-y-1.5">
                                <div class="flex items-center justify-between"><span class="text-[10px] font-bold text-gray-400">{{ ai + 1 }}</span><button @click="removeAccordionItem(selectedBlock, ai)" class="text-red-400 hover:text-red-600"><Trash2 class="w-3 h-3" /></button></div>
                                <input v-model="item.question" :class="ic2" placeholder="Pertanyaan" />
                                <textarea v-model="item.answer" rows="2" :class="ic2" placeholder="Jawaban"></textarea>
                            </div>
                            <button @click="addAccordionItem(selectedBlock)" class="w-full py-1.5 border-2 border-dashed rounded-lg text-xs text-gray-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-1"><Plus class="w-3 h-3" /> Tambah</button>
                        </template>

                        <!-- BUTTON Settings -->
                        <template v-else-if="selectedBlock.type === 'button'">
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Teks</label><input v-model="selectedBlock.data.text" :class="ic" @focus="saveState" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">URL</label><input v-model="selectedBlock.data.url" :class="ic" /></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Variasi</label>
                            <select v-model="selectedBlock.data.variant" :class="ic"><option value="primary">Primary</option><option value="secondary">Secondary</option><option value="outline">Outline</option></select></div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Posisi</label>
                            <select v-model="selectedBlock.data.align" :class="ic"><option value="left">Kiri</option><option value="center">Tengah</option><option value="right">Kanan</option></select></div>
                            <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.fullWidth" class="rounded" /> Full Width</label>
                        </template>

                        <!-- DYNAMIC BLOCK Settings -->
                        <template v-else-if="['latest_articles', 'slider_module', 'events_list', 'facilities_grid', 'program_studies'].includes(selectedBlock.type)">
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-2.5 mb-2">
                                <p class="text-[11px] text-amber-700 dark:text-amber-300 font-medium">⚡ Block Data Dinamis</p>
                                <p class="text-[10px] text-amber-600 dark:text-amber-400">Data diambil otomatis dari database saat halaman ditampilkan.</p>
                            </div>
                            <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Judul Section</label><input v-model="selectedBlock.data.title" :class="ic" @focus="saveState" /></div>

                            <!-- Article specific -->
                            <template v-if="selectedBlock.type === 'latest_articles'">
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Jumlah</label><input type="number" v-model.number="selectedBlock.data.limit" :class="ic" min="1" max="12" /></div>
                                    <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Kolom</label>
                                    <select v-model.number="selectedBlock.data.columns" :class="ic"><option :value="2">2</option><option :value="3">3</option><option :value="4">4</option></select></div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_image" class="rounded" /> Gambar</label>
                                    <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_date" class="rounded" /> Tanggal</label>
                                    <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_category" class="rounded" /> Kategori</label>
                                </div>
                            </template>

                            <!-- Slider specific -->
                            <template v-else-if="selectedBlock.type === 'slider_module'">
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Tinggi (px)</label><input type="number" v-model.number="selectedBlock.data.height" :class="ic" min="200" max="800" /></div>
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.autoplay" class="rounded" /> Autoplay</label>
                                    <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_text" class="rounded" /> Teks</label>
                                </div>
                                <div v-if="selectedBlock.data.autoplay"><label class="block text-[11px] font-medium text-gray-500 mb-1">Interval (ms)</label><input type="number" v-model.number="selectedBlock.data.interval" :class="ic" min="1000" max="10000" step="500" /></div>
                            </template>

                            <!-- Events specific -->
                            <template v-else-if="selectedBlock.type === 'events_list'">
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Jumlah</label><input type="number" v-model.number="selectedBlock.data.limit" :class="ic" min="1" max="12" /></div>
                                    <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Kolom</label>
                                    <select v-model.number="selectedBlock.data.columns" :class="ic"><option :value="2">2</option><option :value="3">3</option><option :value="4">4</option></select></div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_upcoming_only" class="rounded" /> Hanya mendatang</label>
                                    <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_image" class="rounded" /> Gambar</label>
                                </div>
                            </template>

                            <!-- Facilities specific -->
                            <template v-else-if="selectedBlock.type === 'facilities_grid'">
                                <div class="grid grid-cols-2 gap-2">
                                    <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Jumlah</label><input type="number" v-model.number="selectedBlock.data.limit" :class="ic" min="1" max="16" /></div>
                                    <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Kolom</label>
                                    <select v-model.number="selectedBlock.data.columns" :class="ic"><option :value="2">2</option><option :value="3">3</option><option :value="4">4</option></select></div>
                                </div>
                                <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_description" class="rounded" /> Deskripsi</label>
                            </template>

                            <!-- Program Studies specific -->
                            <template v-else-if="selectedBlock.type === 'program_studies'">
                                <div><label class="block text-[11px] font-medium text-gray-500 mb-1">Kolom</label>
                                <select v-model.number="selectedBlock.data.columns" :class="ic"><option :value="2">2</option><option :value="3">3</option><option :value="4">4</option></select></div>
                                <label class="flex items-center gap-1.5 text-xs text-gray-500"><input type="checkbox" v-model="selectedBlock.data.show_degree" class="rounded" /> Jenjang</label>
                            </template>
                        </template>

                        <!-- Block Actions -->
                        <div class="pt-3 border-t border-gray-100 dark:border-zinc-700 flex gap-2">
                            <button @click="duplicateBlock(selectedBlockIndex)" class="flex-1 py-1.5 text-xs font-medium text-gray-500 border rounded-lg hover:border-primary hover:text-primary transition flex items-center justify-center gap-1"><Copy class="w-3 h-3" /> Duplikat</button>
                            <button @click="removeBlock(selectedBlockIndex)" class="flex-1 py-1.5 text-xs font-medium text-red-500 border border-red-200 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition flex items-center justify-center gap-1"><Trash2 class="w-3 h-3" /> Hapus</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="flex-1 overflow-y-auto p-6 flex justify-center">
                <div 
                    class="w-full transition-all duration-300 bg-white dark:bg-zinc-900 shadow-xl rounded-xl"
                    :style="{ maxWidth: previewWidths[previewMode] }"
                >
                    <!-- Empty State -->
                    <div v-if="blocks.length === 0" class="p-20 text-center">
                        <div class="mx-auto w-20 h-20 rounded-2xl bg-gradient-to-br from-primary/20 to-purple-500/20 flex items-center justify-center mb-5">
                            <Plus class="w-10 h-10 text-primary/60" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-400 dark:text-gray-500 mb-2">Mulai Bangun Halaman</h3>
                        <p class="text-sm text-muted-foreground mb-6">Pilih block dari panel kiri untuk memulai</p>
                        <div class="flex justify-center gap-2 flex-wrap">
                            <button v-for="bt in allBlockTypes.slice(0, 4)" :key="bt.type" @click="addBlock(bt.type)" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border text-sm font-medium text-gray-500 hover:border-primary hover:text-primary transition">
                                <component :is="bt.icon" class="w-4 h-4" /> {{ bt.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Blocks -->
                    <draggable 
                        v-else
                        v-model="blocks" 
                        item-key="id" 
                        handle=".drag-handle"
                        ghost-class="opacity-20"
                        :force-fallback="true"
                        :fallback-class="'opacity-50'"
                        :scroll-sensitivity="200"
                        :scroll-speed="20"
                        animation="250"
                        class="min-h-[200px] pb-20"
                    >
                        <template #item="{ element: block, index }">
                            <div>
                                <!-- Insert Between Button -->
                                <div class="relative h-0 flex items-center justify-center z-10 group/insert" @mouseenter="hoverInsertIndex = index" @mouseleave="hoverInsertIndex = -1">
                                    <div v-if="hoverInsertIndex === index" class="absolute -top-3 flex items-center gap-1 bg-white dark:bg-zinc-800 border shadow-lg rounded-full px-2 py-0.5 opacity-0 group-hover/insert:opacity-100 transition-opacity">
                                        <button v-for="bt in allBlockTypes.slice(0, 6)" :key="bt.type" @click="addBlock(bt.type, index)" class="p-1 rounded hover:bg-primary/10 text-gray-400 hover:text-primary transition" :title="bt.label">
                                            <component :is="bt.icon" class="w-3 h-3" />
                                        </button>
                                        <span class="text-[9px] text-gray-300">|</span>
                                        <button class="p-1 rounded hover:bg-primary/10 text-gray-400 hover:text-primary transition" title="Lainnya" @click="sidebarTab = 'blocks'">
                                            <Plus class="w-3 h-3" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Block Item -->
                                <div 
                                    @click="selectedBlockId = block.id; sidebarTab = 'settings'"
                                    class="relative cursor-pointer transition-all group/block"
                                    :class="selectedBlockId === block.id ? 'ring-2 ring-primary ring-offset-0' : 'hover:ring-2 hover:ring-primary/30'"
                                >
                                    <!-- Always-visible drag handle (left edge) -->
                                    <div class="drag-handle absolute left-0 top-0 bottom-0 w-6 flex items-center justify-center cursor-grab active:cursor-grabbing z-20 opacity-0 group-hover/block:opacity-100 transition-opacity bg-gradient-to-r from-gray-200/80 dark:from-zinc-700/80 to-transparent">
                                        <GripVertical class="w-4 h-4 text-gray-400" />
                                    </div>

                                    <!-- Floating Toolbar -->
                                    <div 
                                        v-if="selectedBlockId === block.id"
                                        class="absolute -top-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-0.5 bg-primary text-white rounded-full px-1 py-0.5 shadow-lg"
                                    >
                                        <div class="drag-handle cursor-grab active:cursor-grabbing p-1 rounded-full hover:bg-white/20"><GripVertical class="w-3 h-3" /></div>
                                        <button @click.stop="moveBlock(index, -1)" :disabled="index === 0" class="p-1 rounded-full hover:bg-white/20 disabled:opacity-30"><ChevronUp class="w-3 h-3" /></button>
                                        <button @click.stop="moveBlock(index, 1)" :disabled="index === blocks.length - 1" class="p-1 rounded-full hover:bg-white/20 disabled:opacity-30"><ChevronDown class="w-3 h-3" /></button>
                                        <div class="w-px h-4 bg-white/30 mx-0.5"></div>
                                        <button @click.stop="duplicateBlock(index)" class="p-1 rounded-full hover:bg-white/20"><Copy class="w-3 h-3" /></button>
                                        <button @click.stop="removeBlock(index)" class="p-1 rounded-full hover:bg-red-400"><Trash2 class="w-3 h-3" /></button>
                                    </div>

                                    <!-- Block Content Preview -->
                                    <!-- HERO -->
                                    <div v-if="block.type === 'hero'" class="relative text-center py-20 px-8" :style="{ backgroundColor: block.data.bgColor, color: block.data.textColor, backgroundImage: block.data.bgImage ? `url(${getImgSrc(block.data.bgImage)})` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }">
                                        <div v-if="block.data.bgImage && block.data.overlay" class="absolute inset-0 bg-black/50"></div>
                                        <div class="relative z-10">
                                            <h2 class="text-3xl font-extrabold mb-3">{{ block.data.title }}</h2>
                                            <p class="text-lg opacity-80 mb-6 max-w-xl mx-auto">{{ block.data.subtitle }}</p>
                                            <span v-if="block.data.buttonText" class="inline-block px-6 py-3 bg-white/20 backdrop-blur rounded-xl font-semibold">{{ block.data.buttonText }}</span>
                                        </div>
                                    </div>

                                    <!-- TEXT -->
                                    <div v-else-if="block.type === 'text'" class="px-8 py-6">
                                        <div class="prose prose-sm dark:prose-invert max-w-none" v-html="block.data.content"></div>
                                    </div>

                                    <!-- IMAGE -->
                                    <div v-else-if="block.type === 'image'" :class="block.data.fullWidth ? '' : 'px-8 py-6'">
                                        <div v-if="block.data.url" class="text-center">
                                            <img :src="getImgSrc(block.data.url)" :alt="block.data.alt" class="max-w-full mx-auto" :class="block.data.rounded ? 'rounded-xl' : ''" />
                                            <p v-if="block.data.caption" class="text-sm text-gray-500 mt-2">{{ block.data.caption }}</p>
                                        </div>
                                        <div v-else class="py-12 bg-gray-50 dark:bg-zinc-800 text-center text-gray-400 rounded-xl mx-8 my-6"><Image class="w-10 h-10 mx-auto mb-2 opacity-30" /><p class="text-sm">Masukkan URL gambar</p></div>
                                    </div>

                                    <!-- CARDS -->
                                    <div v-else-if="block.type === 'cards'" class="px-8 py-10">
                                        <h3 v-if="block.data.title" class="text-xl font-bold text-center mb-6">{{ block.data.title }}</h3>
                                        <div class="grid gap-4" :style="{ gridTemplateColumns: `repeat(${block.data.columns}, 1fr)` }">
                                            <div v-for="(card, ci) in block.data.items" :key="ci" class="border rounded-xl p-5 text-center bg-gray-50/50 dark:bg-zinc-800/50 hover:shadow-md transition">
                                                <div class="text-3xl mb-2">{{ card.icon }}</div>
                                                <h4 class="font-bold text-sm mb-1">{{ card.title }}</h4>
                                                <p class="text-xs text-gray-500">{{ card.description }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CTA -->
                                    <div v-else-if="block.type === 'cta'" class="text-center py-16 px-8 text-white" :style="{ backgroundColor: block.data.bgColor }">
                                        <h3 class="text-2xl font-extrabold mb-3">{{ block.data.title }}</h3>
                                        <p class="opacity-80 mb-6 max-w-lg mx-auto">{{ block.data.description }}</p>
                                        <span v-if="block.data.buttonText" class="inline-block px-6 py-3 bg-white text-gray-900 rounded-xl font-semibold shadow-lg">{{ block.data.buttonText }}</span>
                                    </div>

                                    <!-- SPACER -->
                                    <div v-else-if="block.type === 'spacer'" class="flex items-center justify-center" :style="{ height: block.data.height + 'px' }">
                                        <span class="text-[10px] text-gray-300 bg-gray-50 dark:bg-zinc-800 px-2 py-0.5 rounded select-none">{{ block.data.height }}px</span>
                                    </div>

                                    <!-- DIVIDER -->
                                    <div v-else-if="block.type === 'divider'" class="py-4 px-8 flex justify-center">
                                        <hr :style="{ borderStyle: block.data.style, borderColor: block.data.color, width: block.data.width + '%' }" class="border-t-2" />
                                    </div>

                                    <!-- VIDEO -->
                                    <div v-else-if="block.type === 'video'" class="px-8 py-6">
                                        <div v-if="getYouTubeId(block.data.url)" class="aspect-video rounded-xl overflow-hidden shadow-lg"><iframe :src="`https://www.youtube.com/embed/${getYouTubeId(block.data.url)}`" class="w-full h-full" allowfullscreen></iframe></div>
                                        <div v-else class="py-12 bg-gray-50 dark:bg-zinc-800 text-center text-gray-400 rounded-xl"><Play class="w-10 h-10 mx-auto mb-2 opacity-30" /><p class="text-sm">Masukkan URL YouTube</p></div>
                                        <p v-if="block.data.caption" class="text-sm text-gray-500 text-center mt-2">{{ block.data.caption }}</p>
                                    </div>

                                    <!-- GALLERY -->
                                    <div v-else-if="block.type === 'gallery'" class="px-8 py-6">
                                        <div v-if="block.data.images?.length > 0" class="grid gap-3" :style="{ gridTemplateColumns: `repeat(${block.data.columns}, 1fr)` }">
                                            <div v-for="(img, gi) in block.data.images" :key="gi" class="aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-zinc-800">
                                                <img v-if="img.url" :src="getImgSrc(img.url)" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
                                            </div>
                                        </div>
                                        <div v-else class="py-12 bg-gray-50 dark:bg-zinc-800 text-center text-gray-400 rounded-xl"><Images class="w-10 h-10 mx-auto mb-2 opacity-30" /><p class="text-sm">Tambah gambar di panel settings</p></div>
                                    </div>

                                    <!-- COLUMNS -->
                                    <div v-else-if="block.type === 'columns'" class="px-4 py-4" @click.stop>
                                        <div class="grid gap-3" :style="{ gridTemplateColumns: `repeat(${block.data.columns}, 1fr)` }">
                                            <div v-for="(col, ci) in block.data.items" :key="ci" class="border-2 border-dashed border-gray-200 dark:border-zinc-700 rounded-xl p-2 min-h-[100px] bg-gray-50/30 dark:bg-zinc-800/30 flex flex-col">
                                                <!-- Empty column placeholder -->
                                                <div v-if="!col.blocks || col.blocks.length === 0" class="flex-1 flex items-center justify-center">
                                                    <span class="text-[10px] text-gray-300 dark:text-zinc-600">Kolom {{ ci + 1 }} — kosong</span>
                                                </div>
                                                <!-- Nested blocks (inline editable) -->
                                                <div v-else class="space-y-1.5 flex-1">
                                                    <div v-for="(nb, nbi) in col.blocks" :key="nb.id" class="group/nb relative bg-white dark:bg-zinc-900 rounded-lg border border-gray-100 dark:border-zinc-700 hover:border-primary/50 transition-colors">
                                                        <!-- Hover toolbar -->
                                                        <div class="absolute -top-2.5 right-1 z-10 opacity-0 group-hover/nb:opacity-100 transition-opacity flex gap-0.5 bg-gray-800 rounded-md px-1 py-0.5 shadow-lg">
                                                            <button @click.stop="moveNestedBlock(col, nbi, nbi - 1)" :disabled="nbi === 0" class="text-gray-300 hover:text-white p-0.5 disabled:opacity-30"><ChevronUp class="w-3 h-3" /></button>
                                                            <button @click.stop="moveNestedBlock(col, nbi, nbi + 1)" :disabled="nbi >= col.blocks.length - 1" class="text-gray-300 hover:text-white p-0.5 disabled:opacity-30"><ChevronDown class="w-3 h-3" /></button>
                                                            <button @click.stop="removeNestedBlock(col, nbi)" class="text-red-400 hover:text-red-300 p-0.5"><Trash2 class="w-3 h-3" /></button>
                                                        </div>
                                                        <!-- Inline renderers per type -->
                                                        <div v-if="nb.type === 'text'" class="p-2">
                                                            <div class="prose prose-sm dark:prose-invert max-w-none text-xs min-h-[24px] outline-none focus:ring-1 focus:ring-primary/30 rounded px-1" contenteditable @blur="nb.data.content = $event.target.innerHTML" @focus="saveState" v-html="nb.data.content"></div>
                                                        </div>
                                                        <div v-else-if="nb.type === 'image'" class="p-2">
                                                            <div v-if="nb.data.url" class="relative rounded overflow-hidden cursor-pointer" @click.stop="openMediaPicker(false, (img) => { saveState(); nb.data.url = img.url })">
                                                                <img :src="getImgSrc(nb.data.url)" class="w-full h-20 object-cover" />
                                                                <div class="absolute inset-0 bg-black/0 hover:bg-black/30 flex items-center justify-center transition"><span class="text-white text-[10px] font-medium opacity-0 hover:opacity-100">Ganti</span></div>
                                                            </div>
                                                            <button v-else @click.stop="openMediaPicker(false, (img) => { saveState(); nb.data.url = img.url })" class="w-full py-3 bg-gray-50 dark:bg-zinc-800 rounded border border-dashed text-gray-400 text-[10px] hover:border-primary hover:text-primary transition"><Image class="w-4 h-4 mx-auto mb-1" />Pilih Gambar</button>
                                                        </div>
                                                        <div v-else-if="nb.type === 'button'" class="p-2 text-center">
                                                            <input v-model="nb.data.text" class="text-center text-xs px-3 py-1.5 rounded-lg bg-primary/10 text-primary border-0 outline-none w-full font-medium" @focus="saveState" />
                                                            <input v-model="nb.data.url" class="mt-1 text-center text-[9px] text-gray-400 border-0 outline-none w-full bg-transparent" placeholder="URL" />
                                                        </div>
                                                        <div v-else-if="nb.type === 'video'" class="p-2">
                                                            <input v-model="nb.data.url" class="w-full text-[10px] px-2 py-1 bg-gray-50 dark:bg-zinc-800 border rounded text-gray-500 outline-none" placeholder="YouTube URL" @focus="saveState" />
                                                        </div>
                                                        <div v-else-if="nb.type === 'spacer'" class="flex items-center justify-center text-[9px] text-gray-300" :style="{ height: Math.max(nb.data.height / 3, 20) + 'px' }">↕ {{ nb.data.height }}px</div>
                                                        <div v-else-if="nb.type === 'divider'" class="py-2 px-3"><hr :style="{ borderStyle: nb.data.style || 'solid', borderColor: nb.data.color || '#e5e7eb' }" class="border-t" /></div>
                                                        <div v-else class="p-2 flex items-center gap-1.5 text-[10px] text-gray-400">
                                                            <component :is="getBlockTypeInfo(nb.type)?.icon" class="w-3 h-3" />
                                                            <span>{{ getBlockTypeInfo(nb.type)?.label }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Add block button (on canvas) -->
                                                <div class="mt-1.5">
                                                    <select @change="(e) => { if (e.target.value) { addNestedBlock(col, e.target.value); e.target.value = ''; } }" class="w-full px-2 py-1 border border-dashed border-gray-200 dark:border-zinc-700 rounded-lg text-[10px] text-gray-300 dark:text-zinc-600 bg-transparent hover:border-primary hover:text-primary cursor-pointer transition outline-none">
                                                        <option value="">+ Tambah block</option>
                                                        <option v-for="bt in nestedBlockTypes" :key="bt.type" :value="bt.type">{{ bt.label }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- STATS -->
                                    <div v-else-if="block.type === 'stats'" class="py-12 px-8" :style="{ backgroundColor: block.data.bgColor }">
                                        <h3 v-if="block.data.title" class="text-xl font-bold text-center mb-8">{{ block.data.title }}</h3>
                                        <div class="grid grid-cols-4 gap-6">
                                            <div v-for="(item, si) in block.data.items" :key="si" class="text-center">
                                                <div class="text-3xl font-extrabold text-primary mb-1">{{ item.value }}</div>
                                                <div class="text-sm text-gray-500">{{ item.label }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TESTIMONIAL -->
                                    <div v-else-if="block.type === 'testimonial'" class="px-8 py-10">
                                        <div class="grid gap-6" :class="block.data.items.length > 1 ? 'md:grid-cols-2' : ''">
                                            <div v-for="(item, ti) in block.data.items" :key="ti" class="bg-gray-50 dark:bg-zinc-800 rounded-2xl p-6 relative">
                                                <Quote class="w-8 h-8 text-primary/20 absolute top-4 right-4" />
                                                <p class="text-gray-700 dark:text-gray-300 italic mb-4 leading-relaxed">"{{ item.quote }}"</p>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm">{{ item.name?.[0] || '?' }}</div>
                                                    <div><p class="font-semibold text-sm">{{ item.name }}</p><p class="text-xs text-gray-500">{{ item.role }}</p></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ACCORDION -->
                                    <div v-else-if="block.type === 'accordion'" class="px-8 py-10 max-w-3xl mx-auto">
                                        <h3 v-if="block.data.title" class="text-xl font-bold text-center mb-6">{{ block.data.title }}</h3>
                                        <div class="space-y-2">
                                            <div v-for="(item, ai) in block.data.items" :key="ai" class="border rounded-xl overflow-hidden">
                                                <div class="px-5 py-3.5 bg-gray-50 dark:bg-zinc-800 font-medium text-sm flex items-center justify-between">
                                                    {{ item.question }}
                                                    <ChevronRight class="w-4 h-4 text-gray-400" />
                                                </div>
                                                <div class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400 border-t">{{ item.answer }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- BUTTON -->
                                    <div v-else-if="block.type === 'button'" class="px-8 py-6" :class="{ 'text-left': block.data.align === 'left', 'text-center': block.data.align === 'center', 'text-right': block.data.align === 'right' }">
                                        <span 
                                            class="inline-block px-6 py-3 rounded-xl font-semibold text-sm transition"
                                            :class="{
                                                'bg-primary text-white': block.data.variant === 'primary',
                                                'bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-gray-200': block.data.variant === 'secondary',
                                                'border-2 border-primary text-primary': block.data.variant === 'outline',
                                                'w-full': block.data.fullWidth,
                                            }"
                                        >{{ block.data.text }}</span>
                                    </div>

                                    <!-- DYNAMIC BLOCKS (Canvas Preview) -->
                                    <div v-else-if="['latest_articles', 'slider_module', 'events_list', 'facilities_grid', 'program_studies'].includes(block.type)" class="px-8 py-8">
                                        <div class="border-2 border-dashed border-gray-200 dark:border-zinc-700 rounded-xl p-6 bg-gray-50/50 dark:bg-zinc-800/50">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" :class="getBlockTypeInfo(block.type)?.color">
                                                    <component :is="getBlockTypeInfo(block.type)?.icon" class="w-4 h-4" />
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-sm">{{ block.data.title || getBlockTypeInfo(block.type)?.label }}</p>
                                                    <p class="text-[10px] text-gray-400">⚡ Data dinamis — {{ getBlockTypeInfo(block.type)?.desc }}</p>
                                                </div>
                                                <span class="ml-auto px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[9px] font-bold rounded-full uppercase">Live Data</span>
                                            </div>
                                            <div v-if="block.data.limit" class="flex gap-4 text-[10px] text-gray-400">
                                                <span v-if="block.data.limit">📊 {{ block.data.limit }} item</span>
                                                <span v-if="block.data.columns">🔲 {{ block.data.columns }} kolom</span>
                                            </div>
                                            <div class="mt-3 grid gap-2" :style="{ gridTemplateColumns: `repeat(${block.data.columns || 3}, 1fr)` }">
                                                <div v-for="n in Math.min(block.data.limit || 3, 6)" :key="n" class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-100 dark:border-zinc-700 p-3">
                                                    <div class="bg-gray-100 dark:bg-zinc-700 rounded h-12 mb-2"></div>
                                                    <div class="bg-gray-100 dark:bg-zinc-700 rounded h-3 w-3/4 mb-1"></div>
                                                    <div class="bg-gray-50 dark:bg-zinc-600 rounded h-2 w-1/2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </draggable>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Picker Modal -->
    <MediaPicker 
        :show="showMediaPicker" 
        :multiple="mediaPickerMultiple" 
        @close="showMediaPicker = false" 
        @select="onMediaSelected" 
    />
</template>
