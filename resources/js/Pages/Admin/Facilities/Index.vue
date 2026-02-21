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
  Building2, 
  Plus, 
  Search, 
  MoreHorizontal, 
  Pencil, 
  Trash2,
  MapPin,
  CheckCircle2,
  XCircle,
  Users
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
    facilities: Array,
    stats: Object,
});

const form = useForm({});
const search = ref('');

const filteredFacilities = computed(() => {
    if (!search.value) return props.facilities;
    return props.facilities.filter(facility => 
        facility.title.toLowerCase().includes(search.value.toLowerCase()) ||
        (facility.location && facility.location.toLowerCase().includes(search.value.toLowerCase()))
    );
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this facility?')) {
        form.delete(route('facilities.destroy', id));
    }
};
</script>

<template>
    <Head title="Facilities" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Facilities</h1>
                    <p class="text-muted-foreground">
                        Manage faculty buildings, rooms, and labs.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="route('facilities.create')">
                            <Plus class="mr-2 h-4 w-4" /> Add Facility
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Facilities</CardTitle>
                        <Building2 class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.total || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Available resources</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active</CardTitle>
                        <CheckCircle2 class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.active || 0 }}</div>
                        <p class="text-xs text-muted-foreground">In service</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Under Maintenance</CardTitle>
                        <XCircle class="h-4 w-4 text-red-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.inactive || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Currently unavailable</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Card -->
            <Card>
                <CardHeader>
                    <CardTitle>All Facilities</CardTitle>
                    <CardDescription>
                        A list of all facilities including capacity and location.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Search Bar -->
                    <div class="flex items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search facilities..." class="pl-8" v-model="search" />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">ID</TableHead>
                                    <TableHead class="w-[100px]">Image</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Location</TableHead>
                                    <TableHead>Capacity</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="facility in filteredFacilities" :key="facility.id">
                                    <TableCell class="font-medium">{{ facility.id }}</TableCell>
                                    <TableCell>
                                        <div class="h-10 w-16 overflow-hidden rounded-md bg-muted">
                                            <img 
                                                v-if="facility.image" 
                                                :src="`/storage/${facility.image}`" 
                                                class="h-full w-full object-cover" 
                                            />
                                            <div v-else class="flex h-full w-full items-center justify-center bg-gray-100 text-xs text-gray-400">
                                                No Img
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell class="font-medium">{{ facility.title }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center text-sm text-muted-foreground" v-if="facility.location">
                                            <MapPin class="mr-2 h-3.5 w-3.5" />
                                            {{ facility.location }}
                                        </div>
                                        <span v-else>-</span>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex items-center text-sm text-muted-foreground" v-if="facility.capacity">
                                            <Users class="mr-2 h-3.5 w-3.5" />
                                            {{ facility.capacity }} Pax
                                        </div>
                                        <span v-else>-</span>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="facility.is_active ? 'default' : 'secondary'">
                                            {{ facility.is_active ? 'Active' : 'Maintenance' }}
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
                                                    <Link :href="route('facilities.edit', facility.id)" class="cursor-pointer flex items-center">
                                                        <Pencil class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem @click="deleteItem(facility.id)" class="text-red-600 cursor-pointer flex items-center">
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
