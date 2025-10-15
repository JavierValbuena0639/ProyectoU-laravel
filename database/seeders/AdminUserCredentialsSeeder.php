<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminUserCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar roles base
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrador',
                'description' => 'Administrador del sistema con acceso completo',
                'active' => true,
            ]
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Usuario',
                'description' => 'Usuario regular del sistema',
                'active' => true,
            ]
        );

        $supportRole = Role::firstOrCreate(
            ['name' => 'soporte_interno'],
            [
                'display_name' => 'Soporte Interno',
                'description' => 'Rol de soporte con permisos de mantenimiento de BD',
                'active' => true,
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@sumaxia.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Usuario
        User::updateOrCreate(
            ['email' => 'user@sumaxia.com'],
            [
                'name' => 'Usuario',
                'password' => Hash::make('user123'),
                'role_id' => $userRole->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Soporte interno
        User::updateOrCreate(
            ['email' => 'soporte@sumaxia.com'],
            [
                'name' => 'Soporte Interno',
                'password' => Hash::make('soporte123'),
                'role_id' => $supportRole->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}