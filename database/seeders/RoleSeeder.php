<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $rolePenulis = Role::firstOrCreate(['name' => 'penulis']);

        // Create Default Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@fikes.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($roleAdmin);

        // Create Default Penulis User
        $penulis = User::firstOrCreate(
            ['email' => 'penulis@fikes.com'],
            [
                'name' => 'Penulis Demo',
                'password' => Hash::make('password'),
            ]
        );
        $penulis->assignRole($rolePenulis);
    }
}
