<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import InputError from '@/Components/InputError.vue';
import ImageUpload from '@/Components/ImageUpload.vue';

const form = useForm({
    title: '',
    description: '',
    location: '',
    capacity: '',
    image: null,
    is_active: true,
});

const submit = () => {
    form.post(route('facilities.store'));
};
</script>

<template>
    <Head title="Create Facility" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Add Facility
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                            <div>
                                <Label for="title">Name</Label>
                                <Input id="title" type="text" v-model="form.title" required autofocus placeholder="e.g. Laboratorium Keperawatan" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label for="location">Location</Label>
                                    <Input id="location" type="text" v-model="form.location" placeholder="e.g. Gedung A, Lt 2" />
                                    <InputError class="mt-2" :message="form.errors.location" />
                                </div>
                                <div>
                                    <Label for="capacity">Capacity (Optional)</Label>
                                    <Input id="capacity" type="number" v-model="form.capacity" placeholder="e.g. 50" />
                                    <InputError class="mt-2" :message="form.errors.capacity" />
                                </div>
                            </div>

                            <div>
                                <Label for="description">Description</Label>
                                <Textarea id="description" v-model="form.description" rows="4" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <Label>Photo</Label>
                                <ImageUpload v-model="form.image" :error="form.errors.image" />
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <Label htmlFor="is_active">Active</Label>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Save Facility</Button>
                                <Button variant="outline" as-child>
                                    <Link :href="route('facilities.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
