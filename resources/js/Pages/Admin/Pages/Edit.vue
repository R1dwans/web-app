<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import TipTapEditor from '@/Components/TipTapEditor.vue'
import PageBuilder from '@/Components/PageBuilder/PageBuilder.vue'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/Components/ui/card'
import InputError from '@/Components/InputError.vue';
import { ref, watch } from 'vue';
import { ArrowLeft, ExternalLink, PenLine, Blocks } from 'lucide-vue-next';

const props = defineProps({
    page: Object,
});

const form = useForm({
    title: props.page.title,
    slug: props.page.slug,
    content: props.page.content || '',
    blocks: props.page.blocks || [],
    editor_mode: props.page.editor_mode || 'editor',
    is_published: props.page.is_published,
    layout: props.page.layout || 'default',
    meta_title: props.page.meta_title || '',
    meta_description: props.page.meta_description || '',
});

const layoutOptions = [
    { value: 'default', label: 'Default (Sidebar)', icon: 'sidebar-right' },
    { value: 'full', label: 'Full Width', icon: 'full' },
    { value: 'sidebar-left', label: 'Sidebar Kiri', icon: 'sidebar-left' },
    { value: 'centered', label: 'Centered', icon: 'centered' },
];

// Watchers for Auto-SEO (Only if fields are empty)
watch(() => form.title, (newTitle) => {
    if (!form.meta_title) {
        form.meta_title = newTitle;
    }
});

watch(() => form.content, (newContent) => {
    if (!form.meta_description) {
        const plainText = newContent.replace(/<[^>]*>?/gm, '');
        form.meta_description = plainText.substring(0, 160).trim();
    }
});

const submit = (shouldPublish) => {
    form.is_published = shouldPublish;
    form.put(route('pages.update', props.page.id));
};
</script>

<template>
    <Head title="Edit Page" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child>
                        <Link :href="route('pages.index')">
                            <ArrowLeft class="w-4 h-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Edit Page</h1>
                        <p class="text-muted-foreground">Update your static page content.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" as-child v-if="page.is_published">
                        <a :href="`/${page.slug}`" target="_blank">
                            <ExternalLink class="w-4 h-4 mr-2" /> View Public Page
                        </a>
                    </Button>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (Main Content) -->
                <div class="lg:col-span-2 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Page Content</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Title -->
                            <div class="space-y-2">
                                <Label for="title" class="text-lg">Title</Label>
                                <Input id="title" type="text" v-model="form.title" required autofocus class="text-lg font-semibold py-6" />
                                <InputError :message="form.errors.title" />
                            </div>

                            <!-- Slug / Permalink -->
                             <div class="space-y-2">
                                <Label for="slug" class="text-xs text-muted-foreground">Permalink</Label>
                                <div class="flex items-center gap-1 bg-gray-50 dark:bg-zinc-900 rounded-md border px-3 py-1 text-sm text-muted-foreground">
                                    <span>{{ route('welcome') }}</span>
                                    <input 
                                        type="text" 
                                        v-model="form.slug" 
                                        class="bg-transparent border-none focus:ring-0 p-0 text-sm w-full text-foreground font-mono" 
                                        placeholder="url-slug"
                                    />
                                </div>
                                <InputError :message="form.errors.slug" />
                            </div>

                            <!-- Mode Toggle -->
                            <div class="space-y-2">
                                <Label>Mode Editor</Label>
                                <div class="flex bg-gray-100 dark:bg-zinc-800 p-1 rounded-xl w-fit">
                                    <button type="button" @click="form.editor_mode = 'editor'" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all" :class="form.editor_mode === 'editor' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-500 hover:text-gray-700'">
                                        <PenLine class="w-4 h-4" /> Rich Editor
                                    </button>
                                    <button type="button" @click="form.editor_mode = 'builder'" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all" :class="form.editor_mode === 'builder' ? 'bg-white dark:bg-zinc-700 shadow-sm text-primary' : 'text-gray-500 hover:text-gray-700'">
                                        <Blocks class="w-4 h-4" /> Page Builder
                                    </button>
                                </div>
                            </div>

                            <!-- Content Editor (TipTap) -->
                            <div v-if="form.editor_mode === 'editor'" class="space-y-2">
                                <Label>Content</Label>
                                <TipTapEditor v-model="form.content" />
                                <InputError :message="form.errors.content" />
                            </div>

                            <!-- Page Builder -->
                            <div v-else>
                                <PageBuilder v-model="form.blocks" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- SEO Settings -->
                    <Card>
                        <CardHeader>
                            <CardTitle>SEO Settings</CardTitle>
                            <CardDescription>Optimize your page for search engines.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="meta_title">Meta Title</Label>
                                <Input id="meta_title" v-model="form.meta_title" placeholder="Optional. displayed in search results." />
                                <p class="text-xs text-muted-foreground">Left empty, defaults to page title.</p>
                                <InputError :message="form.errors.meta_title" />
                            </div>
                            <div class="space-y-2">
                                <Label for="meta_description">Meta Description</Label>
                                <Textarea id="meta_description" v-model="form.meta_description" placeholder="A brief summary of the page." />
                                <InputError :message="form.errors.meta_description" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="space-y-6">
                    <!-- Publish Status -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Publishing</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex flex-col gap-2">
                                <Button class="w-full" :disabled="form.processing" @click="submit(true)">
                                    Update & Publish
                                </Button>
                                <Button variant="secondary" class="w-full" :disabled="form.processing" @click="submit(false)">
                                    Save as Draft
                                </Button>
                            </div>
                        </CardContent>
                        <CardFooter class="border-t bg-muted/50 py-3 flex flex-col items-start gap-1">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full" :class="page.is_published ? 'bg-green-500' : 'bg-orange-500'"></span>
                                <span class="text-xs font-medium">{{ page.is_published ? 'Currently Published' : 'Currently Draft' }}</span>
                            </div>
                            <p class="text-[10px] text-muted-foreground">
                                Last updated: {{ new Date(page.updated_at).toLocaleString() }}
                            </p>
                        </CardFooter>
                    </Card>

                    <!-- Layout -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Layout</CardTitle>
                            <CardDescription>Pilih tampilan halaman publik.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    v-for="opt in layoutOptions"
                                    :key="opt.value"
                                    type="button"
                                    @click="form.layout = opt.value"
                                    class="flex flex-col items-center gap-1.5 p-3 rounded-lg border-2 transition text-xs font-medium"
                                    :class="form.layout === opt.value ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 hover:border-gray-300 text-gray-500'"
                                >
                                    <div class="w-full h-8 flex gap-0.5 rounded overflow-hidden">
                                        <template v-if="opt.value === 'default'">
                                            <div class="flex-[2] bg-current opacity-20 rounded-sm"></div>
                                            <div class="flex-1 bg-current opacity-10 rounded-sm"></div>
                                        </template>
                                        <template v-else-if="opt.value === 'full'">
                                            <div class="flex-1 bg-current opacity-20 rounded-sm"></div>
                                        </template>
                                        <template v-else-if="opt.value === 'sidebar-left'">
                                            <div class="flex-1 bg-current opacity-10 rounded-sm"></div>
                                            <div class="flex-[2] bg-current opacity-20 rounded-sm"></div>
                                        </template>
                                        <template v-else-if="opt.value === 'centered'">
                                            <div class="flex-1 bg-current opacity-5 rounded-sm"></div>
                                            <div class="flex-[2] bg-current opacity-20 rounded-sm"></div>
                                            <div class="flex-1 bg-current opacity-5 rounded-sm"></div>
                                        </template>
                                    </div>
                                    {{ opt.label }}
                                </button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
