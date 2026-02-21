<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { cn } from '@/lib/utils';
import {
    LayoutDashboard,
    Users,
    BookOpen,
    Calendar,
    Building2,
    FileText,
    Image,
    List,
    LogOut,
    Menu as MenuIcon,
    X,
    UserCog,
    Settings
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
});

const showingNavigationDropdown = ref(false);

const links = [
    { name: 'Dashboard', route: 'dashboard', icon: LayoutDashboard, roles: ['admin', 'penulis'] },
    { name: 'Users', route: 'users.index', icon: Users, roles: ['admin'], active: 'users.*' },
    { name: 'Program Studies', route: 'program-studies.index', icon: BookOpen, roles: ['admin'], active: 'program-studies.*' },
    { name: 'Staff', route: 'staff.index', icon: UserCog, roles: ['admin'], active: 'staff.*' },
    { name: 'Categories', route: 'categories.index', icon: List, roles: ['admin'], active: 'categories.*' },
    { name: 'Menus', route: 'menus.index', icon: MenuIcon, roles: ['admin'], active: 'menus.*' },
    { name: 'Articles', route: 'articles.index', icon: FileText, roles: ['admin', 'penulis'], active: 'articles.*' },
    { name: 'Pages', route: 'pages.index', icon: FileText, roles: ['admin'], active: 'pages.*' },
    { name: 'Events', route: 'events.index', icon: Calendar, roles: ['admin', 'penulis'], active: 'events.*' },
    { name: 'Facilities', route: 'facilities.index', icon: Building2, roles: ['admin', 'penulis'], active: 'facilities.*' },
    { name: 'Documents', route: 'documents.index', icon: FileText, roles: ['admin', 'penulis'], active: 'documents.*' },
    { name: 'Sliders', route: 'sliders.index', icon: Image, roles: ['admin', 'penulis'], active: 'sliders.*' },
    { name: 'Settings', route: 'settings.index', icon: Settings, roles: ['admin'], active: 'settings.*' },
];

function hasRole(allowedRoles) {
    if (!props.user || !props.user.roles) return false;
    return allowedRoles.some(role => props.user.roles.includes(role));
}
</script>

<template>
    <div class="min-h-screen flex">
        <!-- Sidebar for Desktop -->
        <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-white min-h-screen fixed left-0 top-0 bottom-0 z-50">
            <div class="p-6 flex items-center justify-center border-b border-slate-800">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <ApplicationLogo class="block h-9 w-auto fill-current text-white" />
                    <span class="font-bold text-xl tracking-tight">Fikes CMS</span>
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto py-4 dark-scrollbar">
                <nav class="space-y-1 px-3">
                    <template v-for="link in links" :key="link.name">
                        <Link
                            v-if="hasRole(link.roles)"
                            :href="route(link.route)"
                            :class="cn(
                                'flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors',
                                route().current(link.active || link.route) 
                                    ? 'bg-indigo-600 text-white shadow-md' 
                                    : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                            )"
                        >
                            <component :is="link.icon" class="h-5 w-5" />
                            {{ link.name }}
                        </Link>
                    </template>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center gap-3 px-4 py-3 mb-2">
                    <div class="bg-indigo-500 rounded-full p-1">
                        <Users class="h-4 w-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user.name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ user.email }}</p>
                    </div>
                </div>
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button" 
                    class="flex w-full items-center gap-3 px-4 py-2 text-sm font-medium text-red-400 hover:bg-slate-800 hover:text-red-300 rounded-lg transition-colors"
                >
                    <LogOut class="h-5 w-5" />
                    Log Out
                </Link>
            </div>
        </aside>

        <!-- Mobile Header & Overlay -->
        <div class="md:hidden fixed top-0 left-0 right-0 z-50 bg-slate-900 text-white border-b border-slate-800">
            <div class="flex items-center justify-between p-4">
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <ApplicationLogo class="h-8 w-8 fill-current text-white" />
                    <span class="font-bold text-lg">Fikes CMS</span>
                </Link>
                <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="p-2 rounded-md hover:bg-slate-800">
                    <MenuIcon v-if="!showingNavigationDropdown" class="h-6 w-6" />
                    <X v-else class="h-6 w-6" />
                </button>
            </div>
        </div>

        <!-- Mobile Sidebar (Off-canvas) -->
        <div v-show="showingNavigationDropdown" class="md:hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm" @click="showingNavigationDropdown = false">
             <div class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white shadow-xl transform transition-transform duration-300" :class="showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full'" @click.stop>
                 <div class="p-6 border-b border-slate-800 flex justify-between items-center">
                    <span class="font-bold text-xl">Menu</span>
                    <button @click="showingNavigationDropdown = false" class="text-slate-400 hover:text-white">
                        <X class="h-6 w-6" />
                    </button>
                 </div>
                 <div class="flex-1 overflow-y-auto py-4 dark-scrollbar">
                    <nav class="space-y-1 px-3">
                        <template v-for="link in links" :key="link.name">
                            <Link
                                v-if="hasRole(link.roles)"
                                :href="route(link.route)"
                                :class="cn(
                                    'flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors',
                                    route().current(link.active || link.route) 
                                        ? 'bg-indigo-600 text-white shadow-md' 
                                        : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                                )"
                                @click="showingNavigationDropdown = false"
                            >
                                <component :is="link.icon" class="h-5 w-5" />
                                {{ link.name }}
                            </Link>
                        </template>
                    </nav>
                 </div>
                 <div class="p-4 border-t border-slate-800">
                    <Link 
                        :href="route('logout')" 
                        method="post" 
                        as="button" 
                        class="flex w-full items-center gap-3 px-4 py-2 text-sm font-medium text-red-400 hover:bg-slate-800 hover:text-red-300 rounded-lg transition-colors"
                    >
                        <LogOut class="h-5 w-5" />
                        Log Out
                    </Link>
                 </div>
             </div>
        </div>
    </div>
</template>
