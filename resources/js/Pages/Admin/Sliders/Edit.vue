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
    slider: Object,
});

const form = useForm({
    _method: 'PUT',
    title: props.slider.title || '',
    description: props.slider.description || '',
    image: null,
    link: props.slider.link || '',
    order: props.slider.order || 0,
    is_active: Boolean(props.slider.is_active),
});

const submit = () => {
    form.post(route('sliders.update', props.slider.id));
};
</script>

<template>
    <Head title="Edit Slider" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Slider
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                            <div>
                                <Label>Banner Image</Label>
                                <ImageUpload 
                                    v-model="form.image" 
                                    :preview-url="slider.image ? `/storage/${slider.image}` : null"
                                    :error="form.errors.image" 
                                />
                                <p class="text-xs text-gray-500 mt-1">Recommended size: 1920x600px</p>
                            </div>

                            <div>
                                <Label for="title">Title (Optional)</Label>
                                <Input id="title" type="text" v-model="form.title" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <Label for="description">Description (Optional)</Label>
                                <Textarea id="description" v-model="form.description" rows="3" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <Label for="link">CTA Link (Optional)</Label>
                                <Input id="link" type="url" v-model="form.link" placeholder="https://..." />
                                <InputError class="mt-2" :message="form.errors.link" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label for="order">Display Order</Label>
                                    <Input id="order" type="number" v-model="form.order" />
                                    <InputError class="mt-2" :message="form.errors.order" />
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <Label htmlFor="is_active">Active</Label>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Update Slider</Button>
                                <Button variant="outline" as-child>
                                    <Link :href="route('sliders.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
