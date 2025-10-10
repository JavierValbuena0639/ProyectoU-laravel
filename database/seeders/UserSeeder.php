<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $userRole = \App\Models\Role::where('name', 'user')->first();

        $users = [
            [
                'name' => 'Administrador',
                'email' => 'admin@sumaxia.com',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Usuario Demo',
                'email' => 'user@sumaxia.com',
                'password' => Hash::make('user123'),
                'role_id' => $userRole->id,
                'active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            \App\Models\User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
