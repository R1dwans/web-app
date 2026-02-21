<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $primaryMenu = Menu::firstOrCreate(
            ['location' => 'primary'],
            ['name' => 'Main Navigation', 'is_active' => true]
        );

        $items = [
            ['title' => 'Beranda', 'url' => '/', 'order' => 1],
            ['title' => 'Berita', 'url' => '/berita', 'order' => 2],
            ['title' => 'Agenda', 'url' => '/agenda', 'order' => 3],
            ['title' => 'Download', 'url' => '/download', 'order' => 4],
            ['title' => 'Fasilitas', 'url' => '/fasilitas', 'order' => 5],
        ];

        foreach ($items as $item) {
            MenuItem::firstOrCreate(
                [
                    'menu_id' => $primaryMenu->id,
                    'title' => $item['title'],
                ],
                [
                    'url' => $item['url'],
                    'order' => $item['order'],
                ]
            );
        }
    }
}
