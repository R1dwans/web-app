<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Fikes CMS', 'type' => 'text', 'group' => 'general', 'label' => 'Nama Situs'],
            ['key' => 'site_description', 'value' => 'Sistem Informasi Fakultas Ilmu Kesehatan - Universitas Pahlawan Tuanku Tambusai', 'type' => 'textarea', 'group' => 'general', 'label' => 'Deskripsi Situs'],
            ['key' => 'logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Logo'],
            ['key' => 'footer_text', 'value' => '© 2026 Fikes CMS. All rights reserved.', 'type' => 'textarea', 'group' => 'general', 'label' => 'Teks Footer'],

            // Contact
            ['key' => 'contact_email', 'value' => 'info@fikes.ac.id', 'type' => 'text', 'group' => 'contact', 'label' => 'Email'],
            ['key' => 'contact_phone', 'value' => '(0762) 123456', 'type' => 'text', 'group' => 'contact', 'label' => 'Telepon'],
            ['key' => 'contact_address', 'value' => "Jl. Tuanku Tambusai No. 23\nBangkinang Kota, Riau", 'type' => 'textarea', 'group' => 'contact', 'label' => 'Alamat'],

            // Social Media
            ['key' => 'facebook_url', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Facebook URL'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Instagram URL'],
            ['key' => 'youtube_url', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'YouTube URL'],

            // Homepage / Landing Page
            ['key' => 'homepage_display', 'value' => 'default', 'type' => 'select', 'group' => 'homepage', 'label' => 'Tampilan Beranda'],
            ['key' => 'homepage_page_id', 'value' => '', 'type' => 'select', 'group' => 'homepage', 'label' => 'Halaman Statis'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
