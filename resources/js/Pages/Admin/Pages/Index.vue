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
    pages: Array,
});

const form = useForm({});
const search = ref('');

const filteredPages = computed(() => {
    if (!search.value) return props.pages;
    return props.pages.filter(page => 
        page.title.toLowerCase().includes(search.value.toLowerCase()) ||
        page.slug.toLowerCase().includes(search.value.toLowerCase())
    );
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this page?')) {
        form.delete(route('pages.destroy', id));
    }
};

const stats = computed(() => {
    return {
        total: props.pages.length,
        published: props.pages.filter(p => p.is_published).length,
        draft: props.pages.filter(p => !p.is_published).length,
    }
});
</script>

<template>
    <Head title="Pages" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Pages</h1>
                    <p class="text-muted-foreground">
                        Manage your static website pages.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="route('pages.create')">
                            <Plus class="mr-2 h-4 w-4" /> Create Page
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Pages</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                        <p class="text-xs text-muted-foreground">All time pages</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Published</CardTitle>
                        <CheckCircle2 class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.published }}</div>
                        <p class="text-xs text-muted-foreground">Live on site</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Drafts</CardTitle>
                        <XCircle class="h-4 w-4 text-orange-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.draft }}</div>
                        <p class="text-xs text-muted-foreground">Work in progress</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Card -->
            <Card>
                <CardHeader>
                    <CardTitle>All Pages</CardTitle>
                    <CardDescription>
                        A list of all pages including title, slug, and status.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Search Bar -->
                    <div class="flex items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search pages..." class="pl-8" v-model="search" />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Slug</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Last Updated</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="page in filteredPages" :key="page.id">
                                    <TableCell class="font-medium">
                                        <div class="line-clamp-1 max-w-[300px]">{{ page.title }}</div>
                                    </TableCell>
                                    <TableCell>
                                        <code class="text-xs bg-muted px-1 py-0.5 rounded">/{{ page.slug }}</code>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="page.is_published ? 'default' : 'secondary'">
                                            {{ page.is_published ? 'Published' : 'Draft' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-sm text-muted-foreground">
                                        {{ new Date(page.updated_at).toLocaleDateString() }}
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
                                                    <a :href="`/${page.slug}`" target="_blank" class="cursor-pointer flex items-center">
                                                        <FileText class="mr-2 h-4 w-4" /> View
                                                    </a>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem as-child>
                                                    <Link :href="route('pages.edit', page.id)" class="cursor-pointer flex items-center">
                                                        <Pencil class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem @click="deleteItem(page.id)" class="text-red-600 cursor-pointer flex items-center">
                                                    <Trash2 class="mr-2 h-4 w-4" /> Delete
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="filteredPages.length === 0">
                                    <TableCell colspan="5" class="text-center py-10 text-muted-foreground">
                                        No pages found.
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
