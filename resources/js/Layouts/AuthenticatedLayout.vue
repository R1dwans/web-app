<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import { usePage } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-950 flex font-sans text-gray-900 antialiased">
        <Sidebar :user="$page.props.auth.user" />

        <div class="flex-1 flex flex-col md:pl-64 min-h-screen transition-all duration-300">
            <!-- Header for Page Title (Desktop only mostly, mobile has it in sidebar header) -->
            <header class="bg-white dark:bg-zinc-900 shadow-sm border-b border-gray-100 dark:border-zinc-800 sticky top-16 md:top-0 z-30" v-if="$slots.header">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 md:py-6">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-8 pt-20 md:pt-8 bg-gray-50 dark:bg-zinc-950/50">
                 <div class="max-w-7xl mx-auto">
                    <slot />
                 </div>
            </main>
        </div>
    </div>
</template>

