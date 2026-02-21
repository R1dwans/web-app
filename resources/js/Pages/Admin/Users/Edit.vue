<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select'
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.roles[0]?.name || '',
});

const submit = () => {
    form.put(route('users.update', props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit User: {{ user.name }}
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
                                <Label for="email">Email</Label>
                                <Input id="email" type="email" v-model="form.email" required />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <Label for="password">Password (Leave blank to keep current)</Label>
                                <Input id="password" type="password" v-model="form.password" />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div>
                                <Label for="password_confirmation">Confirm Password</Label>
                                <Input id="password_confirmation" type="password" v-model="form.password_confirmation" />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>

                            <div>
                                <Label for="role">Role</Label>
                                <Select v-model="form.role">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                            <div class="flex items-center gap-4">
                                <Button :disabled="form.processing">Update</Button>
                                <Button variant="outline" as-child>
                                    <Link :href="route('users.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
