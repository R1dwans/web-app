<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, computed } from 'vue';
import { Badge } from '@/Components/ui/badge'
import { Input } from '@/Components/ui/input'
import { Search, Users, GraduationCap, UserCog, Shield } from 'lucide-vue-next';

const props = defineProps({
    staff: Array,
    programStudies: Array,
});

const search = ref('');
const activeFilter = ref('all');

const filteredStaff = computed(() => {
    let items = props.staff;
    if (activeFilter.value !== 'all') {
        items = items.filter(s => s.staff_type === activeFilter.value);
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

const filterTabs = [
    { key: 'all', label: 'Semua', icon: Users },
    { key: 'pimpinan', label: 'Pimpinan', icon: Shield },
    { key: 'dosen', label: 'Dosen', icon: GraduationCap },
    { key: 'tendik', label: 'Tendik', icon: UserCog },
];

const getPhotoUrl = (path) => {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
};

const getInitials = (name) => {
    return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
};
</script>

<template>
    <Head title="Staf & Dosen" />

    <PublicLayout>
        <!-- Hero -->
        <div class="bg-gradient-to-br from-primary/10 via-primary/5 to-transparent border-b">
            <div class="container mx-auto px-4 py-16 md:py-20">
                <div class="max-w-3xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-medium mb-6">
                        <Users class="w-4 h-4" />
                        Tenaga Pengajar & Kependidikan
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 dark:text-white mb-4">
                        Staf & Dosen
                    </h1>
                    <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                        Kenali para dosen, tenaga kependidikan, dan pimpinan yang berdedikasi di Fakultas Ilmu Kesehatan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="container mx-auto px-4 py-12">
            <!-- Filters & Search -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10">
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="tab in filterTabs"
                        :key="tab.key"
                        @click="activeFilter = tab.key"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-all"
                        :class="activeFilter === tab.key
                            ? 'bg-primary text-white shadow-lg shadow-primary/25'
                            : 'bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-700'"
                    >
                        <component :is="tab.icon" class="w-4 h-4" />
                        {{ tab.label }}
                    </button>
                </div>
                <div class="relative w-full max-w-sm">
                    <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input v-model="search" placeholder="Cari nama, jabatan, NIP..." class="pl-10" />
                </div>
            </div>

            <!-- Staff Count -->
            <p class="text-sm text-muted-foreground mb-6">
                Menampilkan <strong>{{ filteredStaff.length }}</strong> staf
            </p>

            <!-- Staff Grid - Horizontal Cards -->
            <div v-if="filteredStaff.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-for="s in filteredStaff"
                    :key="s.id"
                    class="group flex bg-[#d6dfe8] dark:bg-zinc-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300"
                >
                    <!-- Photo (Left) -->
                    <div class="w-[180px] min-h-[200px] shrink-0 bg-gray-300 dark:bg-zinc-700 overflow-hidden">
                        <img
                            v-if="s.photo"
                            :src="getPhotoUrl(s.photo)"
                            :alt="s.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center bg-primary/10">
                            <span class="text-4xl font-bold text-primary/40">{{ getInitials(s.name) }}</span>
                        </div>
                    </div>

                    <!-- Info (Right) -->
                    <div class="flex-1 p-5 flex flex-col justify-center gap-2">
                        <!-- Position Badge -->
                        <div>
                            <span class="inline-block bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded">
                                {{ s.position }}
                            </span>
                        </div>

                        <!-- Name -->
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ s.name }}<span v-if="s.title">, {{ s.title }}</span>
                        </h3>

                        <!-- NIP/NIK -->
                        <p v-if="s.nip" class="text-sm text-gray-600 dark:text-gray-400">
                            NIK/: {{ s.nip }}
                        </p>

                        <!-- Program Study -->
                        <p v-if="s.program_study" class="text-sm text-primary italic mt-1">
                            {{ s.program_study.name }}
                        </p>
                        <p v-else class="text-sm text-primary italic mt-1">
                            Fakultas
                        </p>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20">
                <Users class="w-16 h-16 text-muted-foreground/30 mx-auto mb-4" />
                <h3 class="text-xl font-semibold mb-2">Tidak ada staf ditemukan</h3>
                <p class="text-muted-foreground">Coba ubah kata kunci pencarian atau filter.</p>
            </div>
        </div>
    </PublicLayout>
</template>
