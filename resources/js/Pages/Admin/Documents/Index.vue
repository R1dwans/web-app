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
  Lock,
  Globe,
  File
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
    documents: Array,
    stats: Object,
});

const form = useForm({});
const search = ref('');

const filteredDocuments = computed(() => {
    if (!search.value) return props.documents;
    return props.documents.filter(doc => 
        doc.title.toLowerCase().includes(search.value.toLowerCase()) ||
        (doc.category && doc.category.toLowerCase().includes(search.value.toLowerCase()))
    );
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this document?')) {
        form.delete(route('documents.destroy', id));
    }
};

const formatSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    // Simulated size if not available (since backend doesn't pass size yet)
    return 'N/A';
};
</script>

<template>
    <Head title="Documents" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Documents</h1>
                    <p class="text-muted-foreground">
                        Manage academic documents and downloads.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="route('documents.create')">
                            <Plus class="mr-2 h-4 w-4" /> Upload Document
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Documents</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.total || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Uploaded files</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Public</CardTitle>
                        <Globe class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.public || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Visible to everyone</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Private</CardTitle>
                        <Lock class="h-4 w-4 text-orange-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.private || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Restricted access</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Card -->
            <Card>
                <CardHeader>
                    <CardTitle>All Documents</CardTitle>
                    <CardDescription>
                        A list of all documents including category and visibility status.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                     <!-- Search Bar -->
                     <div class="flex items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search documents..." class="pl-8" v-model="search" />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">ID</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>File</TableHead>
                                    <TableHead>Visibility</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="doc in filteredDocuments" :key="doc.id">
                                    <TableCell class="font-medium">{{ doc.id }}</TableCell>
                                    <TableCell>
                                        <div class="font-medium">{{ doc.title }}</div>
                                        <div class="text-xs text-muted-foreground line-clamp-1 max-w-[200px]">{{ doc.description }}</div>
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant="outline">{{ doc.category || 'General' }}</Badge>
                                    </TableCell>
                                    <TableCell>
                                        <a :href="`/storage/${doc.file_path}`" target="_blank" class="flex items-center text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                           <File class="mr-1 h-3.5 w-3.5" /> View
                                        </a>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="doc.is_public ? 'default' : 'secondary'">
                                            {{ doc.is_public ? 'Public' : 'Private' }}
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
                                                    <Link :href="route('documents.edit', doc.id)" class="cursor-pointer flex items-center">
                                                        <Pencil class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem @click="deleteItem(doc.id)" class="text-red-600 cursor-pointer flex items-center">
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
