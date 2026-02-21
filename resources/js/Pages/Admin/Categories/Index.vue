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

defineProps({
    categories: Array,
});

const form = useForm({});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        form.delete(route('categories.destroy', id));
    }
};
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Categories
                </h2>
                <Button as-child>
                    <Link :href="route('categories.create')">Add Category</Link>
                </Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Table>
                            <TableCaption>List of Academic News Categories.</TableCaption>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">ID</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Slug</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="category in categories" :key="category.id">
                                    <TableCell class="font-medium">{{ category.id }}</TableCell>
                                    <TableCell>{{ category.name }}</TableCell>
                                    <TableCell class="text-gray-500 text-sm">{{ category.slug }}</TableCell>
                                    <TableCell class="text-right space-x-2">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="route('categories.edit', category.id)">Edit</Link>
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="deleteItem(category.id)">
                                            Delete
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
