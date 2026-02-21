<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import InputError from '@/Components/InputError.vue';
import ImageUpload from '@/Components/ImageUpload.vue';

const props = defineProps({
    event: Object,
});

// Helper to format date for datetime-local input (YYYY-MM-DDTHH:mm)
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toISOString().slice(0, 16);
};

const form = useForm({
    _method: 'PUT',
    title: props.event.title,
    description: props.event.description || '',
    start_date: formatDateForInput(props.event.start_date),
    end_date: formatDateForInput(props.event.end_date),
    location: props.event.location || '',
    image: null,
    is_published: Boolean(props.event.is_published),
});

const submit = () => {
    form.post(route('events.update', props.event.id));
};
</script>

<template>
    <Head title="Edit Event" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Event
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
                                <ImageUpload 
                                    v-model="form.image" 
                                    :preview-url="event.image ? `/storage/${event.image}` : null"
                                    :error="form.errors.image" 
                                />
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_published" v-model="form.is_published" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <Label htmlFor="is_published">Publish Immediately</Label>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Update Event</Button>
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
