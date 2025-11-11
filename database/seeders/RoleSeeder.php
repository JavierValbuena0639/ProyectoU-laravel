<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Administrador del sistema con acceso completo',
                'active' => true,
            ],
            [
                'name' => 'user',
                'display_name' => 'Usuario',
                'description' => 'Usuario regular del sistema',
                'active' => true,
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Contador',
                'description' => 'Contador con acceso a módulos contables',
                'active' => true,
            ],
            [
                'name' => 'soporte_interno',
                'display_name' => 'Soporte Interno',
                'description' => 'Rol de soporte interno para panel administrativo',
                'active' => true,
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
