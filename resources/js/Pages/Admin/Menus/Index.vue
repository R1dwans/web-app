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
    menus: Array,
});

const form = useForm({});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this menu?')) {
        form.delete(route('menus.destroy', id));
    }
};
</script>

<template>
    <Head title="Menus" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Menus
                </h2>
                <Button as-child>
                    <Link :href="route('menus.create')">Create Menu</Link>
                </Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Table>
                            <TableCaption>List of Menus.</TableCaption>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">ID</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Location</TableHead>
                                    <TableHead>Items</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="menu in menus" :key="menu.id">
                                    <TableCell class="font-medium">{{ menu.id }}</TableCell>
                                    <TableCell>{{ menu.name }}</TableCell>
                                    <TableCell>
                                        <Badge variant="outline" v-if="menu.location">{{ menu.location }}</Badge>
                                        <span v-else class="text-gray-400">-</span>
                                    </TableCell>
                                    <TableCell>{{ menu.items_count }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="menu.is_active ? 'default' : 'secondary'">
                                            {{ menu.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right space-x-2">
                                        <Button variant="outline" size="sm" as-child>
                                            <Link :href="route('menus.edit', menu.id)">Builder</Link>
                                        </Button>
                                        <Button variant="destructive" size="sm" @click="deleteItem(menu.id)">
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
