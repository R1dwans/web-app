<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import InputError from '@/Components/InputError.vue';

const form = useForm({
    name: '',
    location: '',
    is_active: true,
});

const submit = () => {
    form.post(route('menus.store'));
};
</script>

<template>
    <Head title="Create Menu" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Menu
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-xl">
                            <div>
                                <Label for="name">Name</Label>
                                <Input id="name" type="text" v-model="form.name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <Label for="location">Location (Optional)</Label>
                                <Input id="location" type="text" v-model="form.location" placeholder="e.g. primary, footer" />
                                <p class="text-sm text-gray-500">Unique identifier for rendering this menu in specific theme locations.</p>
                                <InputError class="mt-2" :message="form.errors.location" />
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <Label htmlFor="is_active">Active</Label>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Create Menu</Button>
                                <Button variant="outline" as-child>
                                    <Link :href="route('menus.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
