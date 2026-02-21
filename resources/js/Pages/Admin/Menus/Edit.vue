<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import InputError from '@/Components/InputError.vue';
import draggable from 'vuedraggable';
import { GripVertical, Trash2, ExternalLink, ChevronRight, Plus } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    menu: Object,
    articles: Array,
    pages: Array,
    categories: Array,
});

// ─── Menu Settings Form ───────────────────────────────────────────
const menuForm = useForm({
    name: props.menu.name,
    location: props.menu.location,
    is_active: Boolean(props.menu.is_active),
});

const submitMenu = () => {
    menuForm.put(route('menus.update', props.menu.id));
};

// ─── Build nested tree from flat items ────────────────────────────
const buildTree = (items) => {
    return items
        .filter(i => !i.parent_id)
        .sort((a, b) => a.order - b.order)
        .map(parent => ({
            ...parent,
            children: items
                .filter(c => c.parent_id === parent.id)
                .sort((a, b) => a.order - b.order),
        }));
};

const rootItems = ref(buildTree(props.menu.items));

// ─── Save order + parent_id changes ──────────────────────────────
const isSaving = ref(false);
const saveSuccess = ref(false);

const saveOrder = async () => {
    isSaving.value = true;
    const items = [];

    rootItems.value.forEach((item, index) => {
        items.push({ id: item.id, order: index + 1, parent_id: null });
        if (item.children) {
            item.children.forEach((child, ci) => {
                items.push({ id: child.id, order: ci + 1, parent_id: item.id });
            });
        }
    });

    try {
        await axios.post(route('menu-items.reorder'), { items });
        saveSuccess.value = true;
        setTimeout(() => saveSuccess.value = false, 2000);
    } catch (e) {
        console.error('Reorder failed', e);
    } finally {
        isSaving.value = false;
    }
};

const onDragEnd = () => {
    saveOrder();
};

// ─── Add Item Form ────────────────────────────────────────────────
const linkType = ref('custom');

const itemForm = useForm({
    menu_id:       props.menu.id,
    parent_id:     null,
    title:         '',
    url:           '',
    order:         0,
    target:        '_self',
    icon:          '',
    linkable_type: 'custom',
    linkable_id:   null,
});

const resetItemForm = () => {
    itemForm.reset('parent_id', 'title', 'url', 'order', 'target', 'icon', 'linkable_id');
    itemForm.linkable_type = 'custom';
    linkType.value = 'custom';
};

const onLinkTypeChange = () => {
    itemForm.linkable_type = linkType.value;
    itemForm.linkable_id   = null;
    itemForm.url           = '';
};

const onLinkableSelect = (e) => {
    const val = e.target.value;
    itemForm.linkable_id = val ? parseInt(val) : null;
    if (!itemForm.title) {
        const list = linkType.value === 'article' ? props.articles : linkType.value === 'category' ? props.categories : props.pages;
        const item = list?.find(i => i.id === parseInt(val));
        if (item) itemForm.title = item.title || item.name;
    }
};

const submitItem = () => {
    itemForm.post(route('menu-items.store'), {
        onSuccess: () => resetItemForm(),
    });
};

// ─── Delete (axios, no reload) ────────────────────────────────────
const deleteItem = async (id) => {
    if (!confirm('Hapus menu item ini?')) return;

    try {
        await axios.delete(route('menu-items.destroy', id));
        // Remove from local state
        rootItems.value = rootItems.value.filter(item => {
            if (item.id === id) return false;
            if (item.children) {
                item.children = item.children.filter(c => c.id !== id);
            }
            return true;
        });
    } catch (e) {
        console.error('Delete failed', e);
    }
};

// ─── Helpers ──────────────────────────────────────────────────────
const linkTypeLabel = (item) => {
    if (!item.linkable_type) return 'Custom';
    if (item.linkable_type.includes('Article')) return 'Artikel';
    if (item.linkable_type.includes('Page'))    return 'Halaman';
    if (item.linkable_type.includes('Category')) return 'Kategori';
    return 'Custom';
};

const linkTypeBadgeClass = (item) => {
    if (!item.linkable_type) return 'bg-gray-100 text-gray-600';
    if (item.linkable_type.includes('Article')) return 'bg-blue-100 text-blue-700';
    if (item.linkable_type.includes('Page'))    return 'bg-purple-100 text-purple-700';
    if (item.linkable_type.includes('Category')) return 'bg-green-100 text-green-700';
    return 'bg-gray-100 text-gray-600';
};
</script>

<template>
    <Head title="Menu Builder" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Menu Builder: {{ menu.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- ═══ Kiri: Settings + Add Item ═══ -->
                    <div class="md:col-span-1 space-y-6">

                        <!-- Menu Settings -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-medium mb-4">Pengaturan Menu</h3>
                                <form @submit.prevent="submitMenu" class="space-y-4">
                                    <div>
                                        <Label for="name">Nama</Label>
                                        <Input id="name" type="text" v-model="menuForm.name" required />
                                        <InputError class="mt-2" :message="menuForm.errors.name" />
                                    </div>
                                    <div>
                                        <Label for="location">Lokasi</Label>
                                        <Input id="location" type="text" v-model="menuForm.location" />
                                        <InputError class="mt-2" :message="menuForm.errors.location" />
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" id="is_active" v-model="menuForm.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                        <Label htmlFor="is_active">Aktif</Label>
                                    </div>
                                    <Button :disabled="menuForm.processing" class="w-full">Update Menu</Button>
                                </form>
                            </div>
                        </div>

                        <!-- Add Item -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-medium mb-4">Tambah Item</h3>
                                <form @submit.prevent="submitItem" class="space-y-4">
                                    <div>
                                        <Label>Parent (Opsional)</Label>
                                        <select v-model="itemForm.parent_id" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option :value="null">-- Menu Utama (Root) --</option>
                                            <option v-for="parent in rootItems" :key="parent.id" :value="parent.id">{{ parent.title }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <Label>Tipe Link</Label>
                                        <select v-model="linkType" @change="onLinkTypeChange" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="custom">Custom URL</option>
                                            <option value="article">Artikel</option>
                                            <option value="page">Halaman</option>
                                            <option value="category">Kategori</option>
                                        </select>
                                    </div>
                                    <div v-if="linkType === 'article'">
                                        <Label>Pilih Artikel</Label>
                                        <select @change="onLinkableSelect" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Artikel --</option>
                                            <option v-for="article in articles" :key="article.id" :value="article.id">{{ article.title }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="itemForm.errors.linkable_id" />
                                    </div>
                                    <div v-if="linkType === 'page'">
                                        <Label>Pilih Halaman</Label>
                                        <select @change="onLinkableSelect" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Halaman --</option>
                                            <option v-for="page in pages" :key="page.id" :value="page.id">{{ page.title }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="itemForm.errors.linkable_id" />
                                    </div>
                                    <div v-if="linkType === 'category'">
                                        <Label>Pilih Kategori</Label>
                                        <select @change="onLinkableSelect" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="itemForm.errors.linkable_id" />
                                    </div>
                                    <div v-if="linkType === 'custom'">
                                        <Label for="item_url">URL</Label>
                                        <Input id="item_url" type="text" v-model="itemForm.url" placeholder="https://" />
                                        <InputError class="mt-2" :message="itemForm.errors.url" />
                                    </div>
                                    <div>
                                        <Label for="item_title">Judul</Label>
                                        <Input id="item_title" type="text" v-model="itemForm.title" required placeholder="Label yang tampil di menu" />
                                        <InputError class="mt-2" :message="itemForm.errors.title" />
                                    </div>
                                    <div>
                                        <Label>Target</Label>
                                        <select v-model="itemForm.target" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="_self">Same Tab (_self)</option>
                                            <option value="_blank">New Tab (_blank)</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <Label for="item_icon">Icon (Lucide)</Label>
                                            <Input id="item_icon" type="text" v-model="itemForm.icon" placeholder="e.g. Home" />
                                        </div>
                                        <div>
                                            <Label for="item_order">Urutan</Label>
                                            <Input id="item_order" type="number" v-model="itemForm.order" />
                                        </div>
                                    </div>
                                    <Button :disabled="itemForm.processing" variant="secondary" class="w-full">Tambah ke Menu</Button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ═══ Kanan: Daftar Item (Nested Drag & Drop) ═══ -->
                    <div class="md:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="text-lg font-medium">Struktur Menu</h3>
                                    <div class="flex items-center gap-2">
                                        <span v-if="saveSuccess" class="text-xs text-green-600 font-medium animate-pulse">✓ Tersimpan</span>
                                        <span v-if="isSaving" class="text-xs text-gray-400">Menyimpan...</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mb-4">Drag item ke area anak untuk menjadikannya sub-menu. Drag kembali ke area utama untuk membuatnya root.</p>

                                <!-- Root Level Draggable -->
                                <draggable
                                    v-model="rootItems"
                                    item-key="id"
                                    handle=".drag-handle"
                                    ghost-class="drag-ghost"
                                    animation="200"
                                    group="menu-items"
                                    @end="onDragEnd"
                                    class="space-y-2 min-h-[60px]"
                                >
                                    <template #item="{ element: item }">
                                        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                                            <!-- Parent Row -->
                                            <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-gray-100 transition group">
                                                <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 p-1">
                                                    <GripVertical class="w-5 h-5" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-medium text-gray-900 text-sm">{{ item.title }}</span>
                                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium flex-shrink-0" :class="linkTypeBadgeClass(item)">
                                                            {{ linkTypeLabel(item) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-gray-400 truncate">{{ item.url || '—' }}</p>
                                                </div>
                                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                                    <a v-if="item.url" :href="item.url" target="_blank" class="p-1.5 text-gray-400 hover:text-blue-600 rounded"><ExternalLink class="w-3.5 h-3.5" /></a>
                                                    <button type="button" @click="deleteItem(item.id)" class="p-1.5 text-gray-400 hover:text-red-600 rounded"><Trash2 class="w-3.5 h-3.5" /></button>
                                                </div>
                                            </div>

                                            <!-- Children Drop Zone -->
                                            <div class="border-t border-gray-100 bg-gray-25">
                                                <draggable
                                                    v-model="item.children"
                                                    item-key="id"
                                                    handle=".drag-handle"
                                                    ghost-class="drag-ghost"
                                                    animation="200"
                                                    group="menu-items"
                                                    @end="onDragEnd"
                                                    class="min-h-[8px] py-0.5"
                                                >
                                                    <template #item="{ element: child }">
                                                        <div class="flex items-center gap-3 px-4 py-2.5 pl-12 hover:bg-blue-50 transition group">
                                                            <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500 p-0.5">
                                                                <GripVertical class="w-4 h-4" />
                                                            </div>
                                                            <ChevronRight class="w-3 h-3 text-gray-300 flex-shrink-0" />
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-sm text-gray-700">{{ child.title }}</span>
                                                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium flex-shrink-0" :class="linkTypeBadgeClass(child)">
                                                                        {{ linkTypeLabel(child) }}
                                                                    </span>
                                                                </div>
                                                                <p class="text-xs text-gray-400 truncate">{{ child.url || '—' }}</p>
                                                            </div>
                                                            <button type="button" @click="deleteItem(child.id)" class="p-1 text-gray-300 hover:text-red-600 opacity-0 group-hover:opacity-100 transition"><Trash2 class="w-3.5 h-3.5" /></button>
                                                        </div>
                                                    </template>
                                                </draggable>
                                                <!-- Drop hint when empty -->
                                                <div v-if="!item.children || item.children.length === 0" class="text-center py-1 text-[10px] text-gray-300">
                                                    Drop di sini untuk sub-menu
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>

                                <!-- Empty State -->
                                <div v-if="rootItems.length === 0" class="text-center py-12 text-gray-400">
                                    <p class="text-lg mb-1">Belum ada item</p>
                                    <p class="text-sm">Tambahkan menu item dari panel kiri.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.drag-ghost {
    opacity: 0.3;
    background: #e0e7ff;
    border-radius: 0.5rem;
}
</style>
