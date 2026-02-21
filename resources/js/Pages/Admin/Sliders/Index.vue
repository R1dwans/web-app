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

defineProps({
    sliders: Array,
});

const form = useForm({});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this slider?')) {
        form.delete(route('sliders.destroy', id));
    }
};
</script>

<template>
    <Head title="Sliders" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Hero Sliders
                </h2>
                <Button as-child>
                    <Link :href="route('sliders.create')">Add Slider</Link>
                </Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Table>
                            <TableCaption>List of Homepage Sliders.</TableCaption>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">Order</TableHead>
                                    <TableHead class="w-[120px]">Image</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Link</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="slider in sliders" :key="slider.id">
                                    <TableCell class="font-medium">{{ slider.order }}</TableCell>
                                    <TableCell>
                                        <img :src="`/storage/${slider.image}`" class="w-24 h-12 object-cover rounded" />
                                    </TableCell>
                                    <TableCell>
                                        <div class="font-medium">{{ slider.title || '-' }}</div>
                                        <div class="text-xs text-gray-500 line-clamp-1">{{ slider.description }}</div>
                                    </TableCell>
                                    <TableCell>
                                        <a v-if="slider.link" :href="slider.link" target="_blank" class="text-indigo-600 hover:underline text-sm truncate max-w-[200px] block">
                                            {{ slider.link }}
                                        </a>
                                        <span v-else class="text-gray-400">-</span>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="slider.is_active ? 'default' : 'secondary'">
                                            {{ slider.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right space-x-2">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="route('sliders.edit', slider.id)">Edit</Link>
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="deleteItem(slider.id)">
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
