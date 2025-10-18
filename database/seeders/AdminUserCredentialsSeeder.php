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

        // Rol de soporte interno
        $supportRole = Role::firstOrCreate(
            ['name' => 'soporte_interno'],
            [
                'display_name' => 'Soporte Interno',
                'description' => 'Acceso completo a administración de base de datos',
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

        // Soporte Interno reservado para creadores del sistema
        // Crear/actualizar el superusuario de soporte con las credenciales suministradas
        User::updateOrCreate(
            ['email' => 'javi.valbuena0997@gmail.com'],
            [
                'name' => 'Soporte Interno',
                'password' => Hash::make('Aaa.12715!'),
                'role_id' => $supportRole->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Eliminar la cuenta anterior de soporte si existe
        User::where('email', 'soporte@sumaxia.com')->delete();
    }
}