<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { 
  FileText, 
  Plus, 
  Search, 
  MoreHorizontal, 
  Pencil, 
  Trash2,
  CheckCircle2,
  XCircle
} from 'lucide-vue-next'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import { ref, computed } from 'vue';

const props = defineProps({
    articles: Array,
    stats: Object,
});

const form = useForm({});
const search = ref('');

const filteredArticles = computed(() => {
    if (!search.value) return props.articles;
    return props.articles.filter(article => 
        article.title.toLowerCase().includes(search.value.toLowerCase()) ||
        (article.category && article.category.name.toLowerCase().includes(search.value.toLowerCase())) ||
        (article.user && article.user.name.toLowerCase().includes(search.value.toLowerCase()))
    );
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this article?')) {
        form.delete(route('articles.destroy', id));
    }
};
</script>

<template>
    <Head title="Articles" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Articles</h1>
                    <p class="text-muted-foreground">
                        Manage your blog posts and news articles.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="route('articles.create')">
                            <Plus class="mr-2 h-4 w-4" /> Create Article
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Articles</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.total || 0 }}</div>
                        <p class="text-xs text-muted-foreground">All time articles</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Published</CardTitle>
                        <CheckCircle2 class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.published || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Live on site</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Drafts</CardTitle>
                        <XCircle class="h-4 w-4 text-orange-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.draft || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Work in progress</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Card -->
            <Card>
                <CardHeader>
                    <CardTitle>All Articles</CardTitle>
                    <CardDescription>
                        A list of all articles including title, category, author, and status.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Search Bar -->
                    <div class="flex items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search articles..." class="pl-8" v-model="search" />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[80px]">Image</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Author</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="article in filteredArticles" :key="article.id">
                                    <TableCell>
                                        <div class="h-10 w-16 overflow-hidden rounded-md bg-muted">
                                            <img 
                                                v-if="article.image" 
                                                :src="`/storage/${article.image}`" 
                                                alt="Cover" 
                                                class="h-full w-full object-cover" 
                                            />
                                            <div v-else class="flex h-full w-full items-center justify-center bg-gray-100 text-xs text-gray-400">
                                                No Img
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell class="font-medium">
                                        <div class="line-clamp-1 max-w-[200px]">{{ article.title }}</div>
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant="outline" v-if="article.category" class="font-normal">
                                            {{ article.category.name }}
                                        </Badge>
                                        <span v-else class="text-muted-foreground text-sm">-</span>
                                    </TableCell>
                                    <TableCell class="text-sm text-muted-foreground">{{ article.user?.name }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="article.is_published ? 'default' : 'secondary'">
                                            {{ article.is_published ? 'Published' : 'Draft' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" class="h-8 w-8 p-0">
                                                    <span class="sr-only">Open menu</span>
                                                    <MoreHorizontal class="h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                                <DropdownMenuItem as-child>
                                                    <Link :href="route('articles.edit', article.id)" class="cursor-pointer flex items-center">
                                                        <Pencil class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem @click="deleteItem(article.id)" class="text-red-600 cursor-pointer flex items-center">
                                                    <Trash2 class="mr-2 h-4 w-4" /> Delete
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
