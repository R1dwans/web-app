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
  CalendarDays, 
  Plus, 
  Search, 
  MoreHorizontal, 
  Pencil, 
  Trash2,
  MapPin,
  Clock,
  CheckCircle2,
  CalendarClock
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
    events: Array,
    stats: Object,
});

const form = useForm({});
const search = ref('');

const filteredEvents = computed(() => {
    if (!search.value) return props.events;
    return props.events.filter(event => 
        event.title.toLowerCase().includes(search.value.toLowerCase()) ||
        (event.location && event.location.toLowerCase().includes(search.value.toLowerCase()))
    );
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this event?')) {
        form.delete(route('events.destroy', id));
    }
};

function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Events" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Events</h1>
                    <p class="text-muted-foreground">
                        Manage upcoming events and agendas.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button as-child>
                        <Link :href="route('events.create')">
                            <Plus class="mr-2 h-4 w-4" /> Add Event
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Events</CardTitle>
                        <CalendarDays class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.total || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Recorded events</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Upcoming</CardTitle>
                        <CalendarClock class="h-4 w-4 text-indigo-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.upcoming || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Future events</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Published</CardTitle>
                        <CheckCircle2 class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.published || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Visible on site</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Card -->
            <Card>
                <CardHeader>
                    <CardTitle>All Events</CardTitle>
                    <CardDescription>
                        A list of all events including date, location, and status.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <!-- Search Bar -->
                    <div class="flex items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Search events..." class="pl-8" v-model="search" />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">ID</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Location</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="event in filteredEvents" :key="event.id">
                                    <TableCell class="font-medium">{{ event.id }}</TableCell>
                                    <TableCell class="font-medium">{{ event.title }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center text-sm text-muted-foreground">
                                            <CalendarDays class="mr-2 h-3.5 w-3.5" />
                                            {{ formatDate(event.start_date) }}
                                            <span v-if="event.end_date"> - {{ formatDate(event.end_date) }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex items-center text-sm text-muted-foreground" v-if="event.location">
                                            <MapPin class="mr-2 h-3.5 w-3.5" />
                                            {{ event.location }}
                                        </div>
                                        <span v-else>-</span>
                                    </TableCell>
                                    <TableCell>
                                        <Badge :variant="event.is_published ? 'default' : 'secondary'">
                                            {{ event.is_published ? 'Published' : 'Draft' }}
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
                                                    <Link :href="route('events.edit', event.id)" class="cursor-pointer flex items-center">
                                                        <Pencil class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem @click="deleteItem(event.id)" class="text-red-600 cursor-pointer flex items-center">
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
