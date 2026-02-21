<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import TipTapEditor from '@/Components/TipTapEditor.vue'
import ImageUpload from '@/Components/ImageUpload.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select'
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
import { Plus, ArrowLeft } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    article: Object,
    categories: Array,
});

const localCategories = ref([...props.categories]);
const isAddingCategory = ref(false);
const newCategoryName = ref('');
const isCreatingCategory = ref(false);

const createCategory = async () => {
    if (!newCategoryName.value.trim()) return;
    
    isCreatingCategory.value = true;
    try {
        const response = await axios.post(route('categories.store'), {
            name: newCategoryName.value
        });
        
        localCategories.value.push(response.data);
        form.category_id = String(response.data.id);
        isAddingCategory.value = false;
        newCategoryName.value = '';
    } catch (error) {
        console.error('Failed to create category:', error);
         if (error.response && error.response.data.errors) {
            alert(error.response.data.errors.name[0]);
        }
    } finally {
        isCreatingCategory.value = false;
    }
};

const form = useForm({
    _method: 'PUT',
    title: props.article.title,
    slug: props.article.slug,
    content: props.article.content,
    category_id: props.article.category_id ? String(props.article.category_id) : '',
    image: null,
    is_published: Boolean(props.article.is_published),
    layout: props.article.layout || 'default',
    meta_title: props.article.meta_title || '',
    meta_description: props.article.meta_description || '',
    meta_keywords: props.article.meta_keywords || '',
});

const layoutOptions = [
    { value: 'default', label: 'Default (Sidebar)', icon: 'sidebar-right' },
    { value: 'full', label: 'Full Width', icon: 'full' },
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
    form.post(route('articles.update', props.article.id));
};
</script>

<template>
    <Head title="Edit Article" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child>
                        <Link :href="route('articles.index')">
                            <ArrowLeft class="w-4 h-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Edit Article</h1>
                        <p class="text-muted-foreground">Update your blog post.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                   <!-- Actions moved to sidebar -->
                </div>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (Main Content) -->
                <div class="lg:col-span-2 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Article Content</CardTitle>
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
                                    <span>{{ route('articles.index') }}/</span>
                                    <input 
                                        type="text" 
                                        v-model="form.slug" 
                                        class="bg-transparent border-none focus:ring-0 p-0 text-sm w-full text-foreground" 
                                        placeholder="url-slug"
                                    />
                                </div>
                                <InputError :message="form.errors.slug" />
                            </div>

                            <!-- Content Editor -->
                            <div class="space-y-2">
                                <Label>Content</Label>
                                <TipTapEditor v-model="form.content" />
                                <InputError :message="form.errors.content" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- SEO Settings -->
                    <Card>
                        <CardHeader>
                            <CardTitle>SEO Settings</CardTitle>
                            <CardDescription>Optimize your article for search engines.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="meta_title">Meta Title</Label>
                                <Input id="meta_title" v-model="form.meta_title" placeholder="Optional. displayed in search results." />
                                <p class="text-xs text-muted-foreground">Left empty, defaults to article title.</p>
                                <InputError :message="form.errors.meta_title" />
                            </div>
                            <div class="space-y-2">
                                <Label for="meta_description">Meta Description</Label>
                                <Textarea id="meta_description" v-model="form.meta_description" placeholder="A brief summary of the article." />
                                <InputError :message="form.errors.meta_description" />
                            </div>
                            <div class="space-y-2">
                                <Label for="meta_keywords">Meta Keywords</Label>
                                <Input id="meta_keywords" v-model="form.meta_keywords" placeholder="keyword1, keyword2, keyword3" />
                                <InputError :message="form.errors.meta_keywords" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="space-y-6">
                    <!-- Publish Status -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Publish</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex flex-col gap-2">
                                <Button class="w-full" :disabled="form.processing" @click="submit(true)">
                                    Update & Publish
                                </Button>
                                <Button variant="secondary" class="w-full" :disabled="form.processing" @click="submit(false)">
                                    Update Draft
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Categories -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Category</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <Select v-model="form.category_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select Category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="category in localCategories" :key="category.id" :value="String(category.id)">
                                        {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.category_id" />

                            <!-- Inline Add Category -->
                            <div v-if="!isAddingCategory" class="pt-2">
                                <Button type="button" variant="ghost" size="sm" class="h-8 text-xs w-full justify-start px-2" @click="isAddingCategory = true">
                                    <Plus class="w-3 h-3 mr-2" /> Add New Category
                                </Button>
                            </div>
                            <div v-else class="pt-2 flex flex-col gap-2">
                                <Input v-model="newCategoryName" placeholder="Category Name" class="h-8 text-sm" @keydown.enter.prevent="createCategory" />
                                <div class="flex gap-2">
                                    <Button type="button" size="sm" class="h-7 text-xs flex-1" :disabled="isCreatingCategory" @click="createCategory">
                                        {{ isCreatingCategory ? 'Saving...' : 'Add' }}
                                    </Button>
                                    <Button type="button" variant="outline" size="sm" class="h-7 text-xs" @click="isAddingCategory = false">
                                        Cancel
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Feature Image -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Featured Image</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ImageUpload 
                                v-model="form.image" 
                                :error="form.errors.image" 
                                :preview-url="article.image ? `/storage/${article.image}` : null" 
                            />
                        </CardContent>
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
