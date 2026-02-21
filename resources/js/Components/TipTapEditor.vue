<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import Youtube from '@tiptap/extension-youtube'
import Underline from '@tiptap/extension-underline'
import TextAlign from '@tiptap/extension-text-align'
import Highlight from '@tiptap/extension-highlight'
import Color from '@tiptap/extension-color'
import { TextStyle } from '@tiptap/extension-text-style'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import Placeholder from '@tiptap/extension-placeholder'
import { Table } from '@tiptap/extension-table'
import { TableRow } from '@tiptap/extension-table-row'
import { TableCell } from '@tiptap/extension-table-cell'
import { TableHeader } from '@tiptap/extension-table-header'
import { watch, ref, computed } from 'vue'
import { Button } from '@/Components/ui/button'
import {
    Bold, Italic, Underline as UnderlineIcon, Strikethrough,
    Heading1, Heading2, Heading3, Heading4,
    AlignLeft, AlignCenter, AlignRight, AlignJustify,
    List, ListOrdered, Quote, Code, Code2,
    Minus, Undo2, Redo2, Link as LinkIcon, Unlink,
    Image as ImageIcon, Table as TableIcon, Subscript as SubIcon, Superscript as SupIcon,
    Highlighter, Palette, Type, Pilcrow,
    TableProperties, Rows3, Columns3, Trash2
} from 'lucide-vue-next'
import FileManagerModal from '@/Components/FileManagerModal.vue';

// Custom Image extension with size support
const CustomImage = Image.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            width: {
                default: null,
                renderHTML: attributes => {
                    if (!attributes.width) return {};
                    return { width: attributes.width };
                },
            },
            'data-size': {
                default: null,
                renderHTML: attributes => {
                    if (!attributes['data-size']) return {};
                    return { 'data-size': attributes['data-size'], style: `width: ${attributes['data-size']}` };
                },
            },
        };
    },
});

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Mulai menulis konten...' },
})

const emit = defineEmits(['update:modelValue'])

const showFileManager = ref(false);
const showColorPicker = ref(false);
const showHighlightPicker = ref(false);
const showHeadingMenu = ref(false);
const showTableMenu = ref(false);
const colorValue = ref('#000000');
const highlightValue = ref('#fef08a');

const openFileManager = () => { showFileManager.value = true; };

const handleImageSelect = (url) => {
    if (url) {
        editor.value.chain().focus().setImage({ src: url, 'data-size': '75%' }).run();
    }
};

// Image is selected?
const isImageSelected = computed(() => editor.value?.isActive('image'));
const getImageSize = computed(() => {
    if (!editor.value?.isActive('image')) return null;
    const attrs = editor.value.getAttributes('image');
    return attrs['data-size'] || '100%';
});

const setImageSize = (size) => {
    editor.value.chain().focus().updateAttributes('image', { 'data-size': size }).run();
};

const imageSizes = [
    { label: 'Kecil', value: '25%' },
    { label: 'Sedang', value: '50%' },
    { label: 'Besar', value: '75%' },
    { label: 'Penuh', value: '100%' },
];

const setLink = () => {
    const url = window.prompt('URL:');
    if (url) {
        editor.value.chain().focus().setLink({ href: url }).run();
    }
};

const insertTable = () => {
    editor.value.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
    showTableMenu.value = false;
};

const addYoutube = () => {
    const url = window.prompt('YouTube URL:');
    if (url) {
        editor.value.chain().focus().setYoutubeVideo({ src: url }).run();
    }
};

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: { levels: [1, 2, 3, 4] },
            codeBlock: { HTMLAttributes: { class: 'bg-gray-900 text-gray-100 rounded-lg p-4 font-mono text-sm' } },
        }),
        Link.configure({ openOnClick: false }),
        CustomImage.configure({ inline: false, allowBase64: true }),
        Youtube.configure({ controls: true }),
        Underline,
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Highlight.configure({ multicolor: true }),
        Color,
        TextStyle,
        Subscript,
        Superscript,
        Placeholder.configure({ placeholder: props.placeholder }),
        Table.configure({ resizable: true }),
        TableRow,
        TableCell,
        TableHeader,
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm sm:prose max-w-none focus:outline-none min-h-[400px] p-4',
        },
    },
    onUpdate: () => {
        emit('update:modelValue', editor.value.getHTML())
    },
})

watch(() => props.modelValue, (value) => {
    const isSame = editor.value.getHTML() === value
    if (isSame) return
    editor.value.commands.setContent(value, false)
})

const applyColor = (c) => {
    editor.value.chain().focus().setColor(c).run();
    showColorPicker.value = false;
};

const applyHighlight = (c) => {
    editor.value.chain().focus().toggleHighlight({ color: c }).run();
    showHighlightPicker.value = false;
};

const colors = [
    '#000000', '#374151', '#6b7280', '#ef4444', '#f97316', '#eab308',
    '#22c55e', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#ffffff',
];
</script>

<template>
    <div v-if="editor" class="border rounded-lg border-gray-300 dark:border-gray-700 overflow-hidden bg-white dark:bg-zinc-900">

        <!-- Toolbar -->
        <div class="border-b bg-gray-50 dark:bg-zinc-800 px-2 py-1.5 flex flex-wrap items-center gap-0.5 sticky top-0 z-10">

            <!-- Undo / Redo -->
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" title="Undo">
                <Undo2 class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" title="Redo">
                <Redo2 class="w-4 h-4" />
            </Button>

            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

            <!-- Headings dropdown -->
            <div class="relative">
                <Button type="button" size="sm" variant="ghost" class="h-8 text-xs gap-1 px-2"
                    @click="showHeadingMenu = !showHeadingMenu"
                    :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('heading') }"
                >
                    <Pilcrow class="w-3.5 h-3.5" />
                    <span>{{ editor.isActive('heading', { level: 1 }) ? 'H1' : editor.isActive('heading', { level: 2 }) ? 'H2' : editor.isActive('heading', { level: 3 }) ? 'H3' : editor.isActive('heading', { level: 4 }) ? 'H4' : 'Paragraph' }}</span>
                </Button>
                <div v-if="showHeadingMenu" @click="showHeadingMenu = false" class="absolute top-full left-0 mt-1 bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-lg py-1 z-30 w-40">
                    <button type="button" class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().setParagraph().run()">Paragraph</button>
                    <button type="button" class="w-full text-left px-3 py-1.5 text-lg font-bold hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()">Heading 1</button>
                    <button type="button" class="w-full text-left px-3 py-1.5 text-base font-bold hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()">Heading 2</button>
                    <button type="button" class="w-full text-left px-3 py-1.5 text-sm font-bold hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()">Heading 3</button>
                    <button type="button" class="w-full text-left px-3 py-1.5 text-xs font-bold hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().toggleHeading({ level: 4 }).run()">Heading 4</button>
                </div>
            </div>

            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

            <!-- Text Format -->
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleBold().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('bold') }" title="Bold">
                <Bold class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('italic') }" title="Italic">
                <Italic class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleUnderline().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('underline') }" title="Underline">
                <UnderlineIcon class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('strike') }" title="Strikethrough">
                <Strikethrough class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleSubscript().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('subscript') }" title="Subscript">
                <SubIcon class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleSuperscript().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('superscript') }" title="Superscript">
                <SupIcon class="w-4 h-4" />
            </Button>

            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

            <!-- Color Picker -->
            <div class="relative">
                <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="showColorPicker = !showColorPicker; showHighlightPicker = false" title="Warna Teks">
                    <Palette class="w-4 h-4" />
                </Button>
                <div v-if="showColorPicker" class="absolute top-full left-0 mt-1 bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-lg p-2 z-30">
                    <div class="grid grid-cols-6 gap-1 mb-2">
                        <button
                            v-for="c in colors" :key="c" type="button"
                            class="w-6 h-6 rounded border border-gray-200 hover:scale-110 transition"
                            :style="{ backgroundColor: c }"
                            @click="applyColor(c)"
                        ></button>
                    </div>
                    <div class="flex items-center gap-1">
                        <input type="color" v-model="colorValue" class="w-6 h-6 cursor-pointer border-0 p-0" />
                        <button type="button" class="text-xs text-indigo-600 hover:underline" @click="applyColor(colorValue)">Pilih</button>
                    </div>
                </div>
            </div>

            <!-- Highlight Picker -->
            <div class="relative">
                <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="showHighlightPicker = !showHighlightPicker; showColorPicker = false" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('highlight') }" title="Highlight">
                    <Highlighter class="w-4 h-4" />
                </Button>
                <div v-if="showHighlightPicker" class="absolute top-full left-0 mt-1 bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-lg p-2 z-30">
                    <div class="grid grid-cols-6 gap-1 mb-2">
                        <button
                            v-for="c in ['#fef08a', '#bbf7d0', '#bfdbfe', '#fecaca', '#e9d5ff', '#fed7aa']" :key="c" type="button"
                            class="w-6 h-6 rounded border border-gray-200 hover:scale-110 transition"
                            :style="{ backgroundColor: c }"
                            @click="applyHighlight(c)"
                        ></button>
                    </div>
                    <button type="button" class="text-xs text-gray-500 hover:underline" @click="editor.chain().focus().unsetHighlight().run(); showHighlightPicker = false">Hapus</button>
                </div>
            </div>

            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

            <!-- Alignment -->
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().setTextAlign('left').run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive({ textAlign: 'left' }) }" title="Rata Kiri">
                <AlignLeft class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().setTextAlign('center').run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive({ textAlign: 'center' }) }" title="Rata Tengah">
                <AlignCenter class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().setTextAlign('right').run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive({ textAlign: 'right' }) }" title="Rata Kanan">
                <AlignRight class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().setTextAlign('justify').run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive({ textAlign: 'justify' }) }" title="Rata Kanan Kiri">
                <AlignJustify class="w-4 h-4" />
            </Button>

            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

            <!-- Lists & Block -->
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('bulletList') }" title="Bullet List">
                <List class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('orderedList') }" title="Numbered List">
                <ListOrdered class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('blockquote') }" title="Blockquote">
                <Quote class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleCode().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('code') }" title="Inline Code">
                <Code class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().toggleCodeBlock().run()" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('codeBlock') }" title="Code Block">
                <Code2 class="w-4 h-4" />
            </Button>
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().setHorizontalRule().run()" title="Separator">
                <Minus class="w-4 h-4" />
            </Button>

            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

            <!-- Link -->
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="setLink" :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('link') }" title="Insert Link">
                <LinkIcon class="w-4 h-4" />
            </Button>
            <Button v-if="editor.isActive('link')" type="button" size="icon" variant="ghost" class="h-8 w-8" @click="editor.chain().focus().unsetLink().run()" title="Remove Link">
                <Unlink class="w-4 h-4" />
            </Button>

            <!-- Image -->
            <Button type="button" size="icon" variant="ghost" class="h-8 w-8" @click="openFileManager" title="Insert Image">
                <ImageIcon class="w-4 h-4" />
            </Button>

            <!-- Image Size Controls (visible when image selected) -->
            <template v-if="isImageSelected">
                <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                <div class="flex items-center gap-0.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-md px-1 py-0.5">
                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium px-1">Ukuran:</span>
                    <Button
                        v-for="s in imageSizes" :key="s.value"
                        type="button" size="sm" variant="ghost"
                        class="h-7 text-xs px-2"
                        :class="{ 'bg-indigo-200 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200': getImageSize === s.value }"
                        @click="setImageSize(s.value)"
                    >
                        {{ s.label }}
                    </Button>
                </div>
            </template>

            <!-- Table -->
            <div class="relative">
                <Button type="button" size="icon" variant="ghost" class="h-8 w-8"
                    @click="showTableMenu = !showTableMenu"
                    :class="{ 'bg-gray-200 dark:bg-gray-700': editor.isActive('table') }"
                    title="Table"
                >
                    <TableIcon class="w-4 h-4" />
                </Button>
                <div v-if="showTableMenu" @click.stop class="absolute top-full right-0 mt-1 bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded-lg shadow-lg py-1 z-30 w-48">
                    <button v-if="!editor.isActive('table')" type="button" class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center gap-2" @click="insertTable">
                        <TableIcon class="w-3.5 h-3.5" /> Sisipkan Tabel 3×3
                    </button>
                    <template v-if="editor.isActive('table')">
                        <button type="button" class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().addColumnAfter().run(); showTableMenu = false">+ Kolom Kanan</button>
                        <button type="button" class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().addRowAfter().run(); showTableMenu = false">+ Baris Bawah</button>
                        <button type="button" class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().deleteColumn().run(); showTableMenu = false">Hapus Kolom</button>
                        <button type="button" class="w-full text-left px-3 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-zinc-700" @click="editor.chain().focus().deleteRow().run(); showTableMenu = false">Hapus Baris</button>
                        <div class="border-t dark:border-zinc-700 my-1"></div>
                        <button type="button" class="w-full text-left px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" @click="editor.chain().focus().deleteTable().run(); showTableMenu = false">Hapus Tabel</button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Editor Content -->
        <editor-content :editor="editor" />

        <FileManagerModal
            v-model:open="showFileManager"
            @select="handleImageSelect"
        />
    </div>
</template>

<style>
/* TipTap editor styles */
.ProseMirror {
    min-height: 400px;
}
.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: #adb5bd;
    pointer-events: none;
    height: 0;
}
.ProseMirror table {
    border-collapse: collapse;
    width: 100%;
    margin: 1rem 0;
}
.ProseMirror table td,
.ProseMirror table th {
    border: 1px solid #d1d5db;
    padding: 0.5rem 0.75rem;
    text-align: left;
    min-width: 80px;
}
.ProseMirror table th {
    background: #f3f4f6;
    font-weight: 600;
}
.ProseMirror blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1rem;
    margin-left: 0;
    color: #6b7280;
    font-style: italic;
}
.ProseMirror hr {
    border: none;
    border-top: 2px solid #e5e7eb;
    margin: 1.5rem 0;
}
.ProseMirror img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: box-shadow 0.2s;
}
.ProseMirror img.ProseMirror-selectednode {
    outline: 3px solid #6366f1;
    outline-offset: 2px;
    border-radius: 0.5rem;
}
.ProseMirror img[data-size] {
    display: block;
    margin: 0.5rem 0;
}
.ProseMirror code {
    background: #f3f4f6;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
    color: #e11d48;
}
.ProseMirror pre code {
    background: none;
    padding: 0;
    color: inherit;
}
</style>
