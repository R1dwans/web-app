<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import ImageUpload from '@/Components/ImageUpload.vue'
import {
  Select, SelectContent, SelectItem, SelectTrigger, SelectValue,
} from '@/Components/ui/select'
import {
  Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle,
} from '@/Components/ui/card'
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';
import { Checkbox } from '@/Components/ui/checkbox';

const props = defineProps({
    staff: Object,
    programStudies: Array,
});

const form = useForm({
    _method: 'PUT',
    name: props.staff.name,
    nip: props.staff.nip || '',
    position: props.staff.position,
    title: props.staff.title || '',
    email: props.staff.email || '',
    phone: props.staff.phone || '',
    photo: null,
    education: props.staff.education || '',
    expertise: props.staff.expertise || '',
    bio: props.staff.bio || '',
    program_study_id: props.staff.program_study_id ? String(props.staff.program_study_id) : '',
    staff_type: props.staff.staff_type || 'dosen',
    order: props.staff.order || 0,
    is_active: Boolean(props.staff.is_active),
});

const submit = () => {
    form.post(route('staff.update', props.staff.id));
};
</script>

<template>
    <Head title="Edit Staf" />

    <AuthenticatedLayout>
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('staff.index')">
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Edit Staf</h1>
                    <p class="text-muted-foreground">Perbarui data {{ staff.name }}.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Pribadi</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="name">Nama Lengkap *</Label>
                                    <Input id="name" v-model="form.name" required placeholder="Nama lengkap tanpa gelar" />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="title">Gelar</Label>
                                    <Input id="title" v-model="form.title" placeholder="S.Kep., Ns., M.Kep." />
                                    <InputError :message="form.errors.title" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="nip">NIP</Label>
                                    <Input id="nip" v-model="form.nip" placeholder="19xxxxxxxxxx" />
                                    <InputError :message="form.errors.nip" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="position">Jabatan *</Label>
                                    <Input id="position" v-model="form.position" required placeholder="Dosen Tetap, Kaprodi, dll" />
                                    <InputError :message="form.errors.position" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input id="email" type="email" v-model="form.email" placeholder="email@domain.ac.id" />
                                    <InputError :message="form.errors.email" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="phone">Telepon</Label>
                                    <Input id="phone" v-model="form.phone" placeholder="08xxxxxxxxxx" />
                                    <InputError :message="form.errors.phone" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Academic Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Akademik</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="education">Pendidikan Terakhir</Label>
                                <Input id="education" v-model="form.education" placeholder="S2 Keperawatan - Universitas ..." />
                                <InputError :message="form.errors.education" />
                            </div>
                            <div class="space-y-2">
                                <Label for="expertise">Bidang Keahlian</Label>
                                <Textarea id="expertise" v-model="form.expertise" placeholder="Keperawatan Medikal Bedah, Manajemen Keperawatan, ..." rows="3" />
                                <InputError :message="form.errors.expertise" />
                            </div>
                            <div class="space-y-2">
                                <Label for="bio">Biografi Singkat</Label>
                                <Textarea id="bio" v-model="form.bio" placeholder="Tuliskan biografi singkat..." rows="4" />
                                <InputError :message="form.errors.bio" />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="space-y-6">
                    <!-- Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Simpan</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <Button class="w-full" :disabled="form.processing">
                                Update Data Staf
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Classification -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Klasifikasi</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label>Tipe Staf *</Label>
                                <Select v-model="form.staff_type">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih tipe" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="dosen">Dosen</SelectItem>
                                        <SelectItem value="tendik">Tenaga Kependidikan</SelectItem>
                                        <SelectItem value="pimpinan">Pimpinan</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.staff_type" />
                            </div>
                            <div class="space-y-2">
                                <Label>Program Studi</Label>
                                <Select v-model="form.program_study_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih Prodi (opsional)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="ps in programStudies" :key="ps.id" :value="String(ps.id)">
                                            {{ ps.name }} ({{ ps.degree }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.program_study_id" />
                            </div>
                            <div class="space-y-2">
                                <Label for="order">Urutan</Label>
                                <Input id="order" type="number" v-model="form.order" />
                            </div>
                            <div class="flex items-center gap-3 pt-2">
                                <Checkbox id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                                <Label for="is_active">Status Aktif</Label>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Photo -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base">Foto</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ImageUpload
                                v-model="form.photo"
                                :error="form.errors.photo"
                                :preview-url="staff.photo ? `/storage/${staff.photo}` : null"
                            />
                        </CardContent>
                    </Card>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
