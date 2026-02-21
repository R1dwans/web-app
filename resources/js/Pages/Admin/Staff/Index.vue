<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/Components/ui/table'
import {
  Card, CardContent, CardDescription, CardHeader, CardTitle,
} from '@/Components/ui/card'
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Badge } from '@/Components/ui/badge'
import { ref, computed } from 'vue';
import { Plus, Search, MoreHorizontal, Pencil, Trash2, Users, GraduationCap, UserCog, Shield } from 'lucide-vue-next';

const props = defineProps({
    staff: Array,
    stats: Object,
});

const search = ref('');
const filterType = ref('all');

const filteredStaff = computed(() => {
    let items = props.staff;
    if (filterType.value !== 'all') {
        items = items.filter(s => s.staff_type === filterType.value);
    }
    if (search.value) {
        const q = search.value.toLowerCase();
        items = items.filter(s =>
            s.name.toLowerCase().includes(q) ||
            s.position.toLowerCase().includes(q) ||
            (s.nip && s.nip.includes(q))
        );
    }
    return items;
});

const deleteForm = useForm({});
const deleteItem = (id) => {
    if (confirm('Yakin ingin menghapus data staf ini?')) {
        deleteForm.delete(route('staff.destroy', id));
    }
};

const typeLabel = (type) => {
    const labels = { dosen: 'Dosen', tendik: 'Tendik', pimpinan: 'Pimpinan' };
    return labels[type] || type;
};

const typeBadgeClass = (type) => {
    const cls = {
        dosen: 'bg-blue-100 text-blue-700',
        tendik: 'bg-green-100 text-green-700',
        pimpinan: 'bg-purple-100 text-purple-700',
    };
    return cls[type] || 'bg-gray-100 text-gray-700';
};

const getPhotoUrl = (path) => {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
};
</script>

<template>
    <Head title="Staff Management" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Manajemen Staf</h1>
                    <p class="text-muted-foreground">Kelola data dosen, tenaga kependidikan, dan pimpinan.</p>
                </div>
                <Button as-child>
                    <Link :href="route('staff.create')">
                        <Plus class="mr-2 h-4 w-4" /> Tambah Staf
                    </Link>
                </Button>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card class="cursor-pointer hover:shadow-md transition" @click="filterType = 'all'">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Staf</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.total || 0 }}</div>
                        <p class="text-xs text-muted-foreground">{{ stats?.active || 0 }} aktif</p>
                    </CardContent>
                </Card>
                <Card class="cursor-pointer hover:shadow-md transition" @click="filterType = 'dosen'">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Dosen</CardTitle>
                        <GraduationCap class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.dosen || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Tenaga pengajar</p>
                    </CardContent>
                </Card>
                <Card class="cursor-pointer hover:shadow-md transition" @click="filterType = 'tendik'">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Tendik</CardTitle>
                        <UserCog class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.tendik || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Tenaga kependidikan</p>
                    </CardContent>
                </Card>
                <Card class="cursor-pointer hover:shadow-md transition" @click="filterType = 'pimpinan'">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pimpinan</CardTitle>
                        <Shield class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats?.pimpinan || 0 }}</div>
                        <p class="text-xs text-muted-foreground">Jabatan struktural</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Data Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Data Staf</CardTitle>
                    <CardDescription>
                        {{ filterType === 'all' ? 'Semua staf' : `Filter: ${typeLabel(filterType)}` }}
                        <button v-if="filterType !== 'all'" @click="filterType = 'all'" class="ml-2 text-primary underline text-xs">Reset</button>
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input placeholder="Cari nama, jabatan, NIP..." class="pl-8" v-model="search" />
                        </div>
                    </div>

                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[60px]">Foto</TableHead>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>NIP</TableHead>
                                    <TableHead>Jabatan</TableHead>
                                    <TableHead>Tipe</TableHead>
                                    <TableHead>Prodi</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="s in filteredStaff" :key="s.id">
                                    <TableCell>
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                            <img v-if="s.photo" :src="getPhotoUrl(s.photo)" class="w-full h-full object-cover" :alt="s.name" />
                                            <Users v-else class="w-5 h-5 text-gray-400" />
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div>
                                            <span class="font-medium">{{ s.name }}</span>
                                            <span v-if="s.title" class="text-xs text-muted-foreground ml-1">{{ s.title }}</span>
                                        </div>
                                        <p v-if="s.email" class="text-xs text-muted-foreground">{{ s.email }}</p>
                                    </TableCell>
                                    <TableCell class="text-sm text-muted-foreground">{{ s.nip || '—' }}</TableCell>
                                    <TableCell class="text-sm">{{ s.position }}</TableCell>
                                    <TableCell>
                                        <Badge variant="secondary" :class="typeBadgeClass(s.staff_type)">
                                            {{ typeLabel(s.staff_type) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-sm text-muted-foreground">{{ s.program_study?.name || '—' }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="s.is_active ? 'default' : 'outline'">
                                            {{ s.is_active ? 'Aktif' : 'Nonaktif' }}
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
                                                <DropdownMenuItem as-child>
                                                    <Link :href="route('staff.edit', s.id)" class="flex items-center">
                                                        <Pencil class="mr-2 h-4 w-4" /> Edit
                                                    </Link>
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="deleteItem(s.id)" class="text-destructive">
                                                    <Trash2 class="mr-2 h-4 w-4" /> Hapus
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="filteredStaff.length === 0">
                                    <TableCell colspan="8" class="text-center py-12 text-muted-foreground">
                                        Tidak ada data staf ditemukan.
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
