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
  BookOpen, 
  Plus, 
  Search, 
  MoreHorizontal, 
  Pencil, 
  Trash2,
  GraduationCap
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
    programStudies: Array,
    stats: Object,
});

const form = useForm({});
const search = ref('');

const filteredProgramStudies = computed(() => {
    if (!search.value) return props.programStudies;
    return props.programStudies.filter(ps => 
        ps.name.toLowerCase().includes(search.value.toLowerCase()) ||
        ps.degree.toLowerCase().includes(search.value.toLowerCase())
    );
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this program study?')) {
        form.delete(route('program-studies.destroy', id));
    }
};
</script>

<template>
    <Head title="Program Studies" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Program Studies</h1>
                    <p class="text-muted-foreground">
                        Manage faculty program studies and degrees.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="route('program-studies.create')">
                            <Plus class="mr-2 h-4 w-4" /> Add Program
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Programs</CardTitle>
                        <BookOpen class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.total || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Active study programs</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Bachelor (S1)</CardTitle>
                        <GraduationCap class="h-4 w-4 text-indigo-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.bachelor || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Undergraduate programs</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Diploma (D3)</CardTitle>
                        <GraduationCap class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.diploma || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Vocational programs</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Card -->
            <Card>
                <CardHeader>
                    <CardTitle>All Programs</CardTitle>
                    <CardDescription>
                        A list of all study programs offered by the faculty.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Search Bar -->
                    <div class="flex items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search programs..." class="pl-8" v-model="search" />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">ID</TableHead>
                                    <TableHead>Degree</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Slug</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="ps in filteredProgramStudies" :key="ps.id">
                                    <TableCell class="font-medium">{{ ps.id }}</TableCell>
                                    <TableCell>
                                        <Badge variant="secondary">{{ ps.degree }}</Badge>
                                    </TableCell>
                                    <TableCell class="font-medium">{{ ps.name }}</TableCell>
                                    <TableCell class="text-muted-foreground text-sm">{{ ps.slug }}</TableCell>
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
                                                    <Link :href="route('program-studies.edit', ps.id)" class="cursor-pointer flex items-center">
                                                        <Pencil class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem @click="deleteItem(ps.id)" class="text-red-600 cursor-pointer flex items-center">
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
