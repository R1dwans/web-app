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
    start_date: '',
    end_date: '',
    location: '',
    image: null,
    is_published: true,
});

const submit = () => {
    form.post(route('events.store'));
};
</script>

<template>
    <Head title="Create Event" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Event
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                            <div>
                                <Label for="title">Event Title</Label>
                                <Input id="title" type="text" v-model="form.title" required autofocus />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label for="start_date">Start Date</Label>
                                    <Input id="start_date" type="datetime-local" v-model="form.start_date" required />
                                    <InputError class="mt-2" :message="form.errors.start_date" />
                                </div>
                                <div>
                                    <Label for="end_date">End Date</Label>
                                    <Input id="end_date" type="datetime-local" v-model="form.end_date" />
                                    <InputError class="mt-2" :message="form.errors.end_date" />
                                </div>
                            </div>

                            <div>
                                <Label for="location">Location</Label>
                                <Input id="location" type="text" v-model="form.location" placeholder="e.g. Ruang Rapat Dekanat" />
                                <InputError class="mt-2" :message="form.errors.location" />
                            </div>

                            <div>
                                <Label for="description">Description (Optional)</Label>
                                <Textarea id="description" v-model="form.description" rows="5" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <Label>Event Banner (Optional)</Label>
                                <ImageUpload v-model="form.image" :error="form.errors.image" />
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_published" v-model="form.is_published" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <Label htmlFor="is_published">Publish Immediately</Label>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Create Event</Button>
                                <Button variant="outline" as-child>
                                    <Link :href="route('events.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
