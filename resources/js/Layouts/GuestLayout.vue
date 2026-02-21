<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const page = usePage();
const s = computed(() => page.props.appSettings || {});
const siteName = computed(() => s.value.site_name || 'Fikes CMS');
const logoUrl = computed(() => s.value.logo ? `/storage/${s.value.logo}` : null);
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-zinc-950 p-4">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <Link href="/" class="inline-block">
                    <img v-if="logoUrl" :src="logoUrl" class="h-16 w-auto mx-auto" :alt="siteName" />
                    <ApplicationLogo v-else class="h-16 w-16 fill-current text-indigo-600 mx-auto" />
                </Link>
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Masuk ke Akun Anda
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Selamat datang di {{ siteName }}
                </p>
            </div>

            <div class="bg-white dark:bg-zinc-900 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100 dark:border-zinc-800">
                <slot />
            </div>
        </div>
    </div>
</template>
