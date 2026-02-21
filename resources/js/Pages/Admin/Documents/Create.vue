<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import InputError from '@/Components/InputError.vue';
import FileUpload from '@/Components/FileUpload.vue';

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select'

const form = useForm({
    title: '',
    description: '',
    category: '',
    file: null,
    is_public: true,
});

const submit = () => {
    form.post(route('documents.store'));
};

const categories = ['Akademik', 'Kemahasiswaan', 'Surat Keputusan (SK)', 'Formulir', 'Jadwal', 'Lainnya'];
</script>

<template>
    <Head title="Upload Document" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Upload Document
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                            <div>
                                <Label for="title">Document Title</Label>
                                <Input id="title" type="text" v-model="form.title" required autofocus placeholder="e.g. Kalender Akademik 2024/2025" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <Label for="category">Category</Label>
                                <Select v-model="form.category">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cat in categories" :key="cat" :value="cat">
                                            {{ cat }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError class="mt-2" :message="form.errors.category" />
                            </div>

                            <div>
                                <Label for="description">Description</Label>
                                <Textarea id="description" v-model="form.description" rows="3" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div>
                                <Label>File</Label>
                                <FileUpload 
                                    v-model="form.file" 
                                    :error="form.errors.file"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                    label="PDF, DOCX, XLSX, PPTX"
                                    :max-size-mb="6"
                                />
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="is_public" v-model="form.is_public" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <Label htmlFor="is_public">Publicly Available</Label>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Upload Document</Button>
                                <Button variant="outline" as-child>
                                    <Link :href="route('documents.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
