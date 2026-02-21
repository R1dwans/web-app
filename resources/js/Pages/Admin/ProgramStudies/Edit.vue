<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select'
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    programStudy: Object,
});

const form = useForm({
    name: props.programStudy.name,
    degree: props.programStudy.degree,
    description: props.programStudy.description || '',
});

const submit = () => {
    form.put(route('program-studies.update', props.programStudy.id));
};
</script>

<template>
    <Head title="Edit Program Study" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Program Study: {{ programStudy.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6 max-w-xl">
                            <div>
                                <Label for="degree">Degree</Label>
                                <Select v-model="form.degree">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a degree" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="D3">D3 (Diploma III)</SelectItem>
                                        <SelectItem value="D4">D4 (Diploma IV)</SelectItem>
                                        <SelectItem value="S1">S1 (Sarjana)</SelectItem>
                                        <SelectItem value="S2">S2 (Magister)</SelectItem>
                                        <SelectItem value="Profesi">Profesi</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError class="mt-2" :message="form.errors.degree" />
                            </div>

                            <div>
                                <Label for="name">Name</Label>
                                <Input id="name" type="text" v-model="form.name" required autofocus />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <Label for="description">Description</Label>
                                <Textarea id="description" v-model="form.description" />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Update</Button>
                                <Button variant="outline" as-child>
                                    <Link :href="route('program-studies.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
