<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Role;
use App\Mail\VerificationCodeMail;

class AdminUserCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles requeridos
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrador', 'description' => 'Administrador del sistema', 'active' => true]);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['display_name' => 'Usuario', 'description' => 'Usuario estándar', 'active' => true]);
        $supportRole = Role::firstOrCreate(['name' => 'soporte_interno'], ['display_name' => 'Soporte Interno', 'description' => 'Acceso completo a administración de base de datos', 'active' => true]);

        // Usuario administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin@sumaxia.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'active' => true,
                'email_verified_at' => null,
            ]
        );

        // Usuario demo
        $demoUser = User::updateOrCreate(
            ['email' => 'user@sumaxia.com'],
            [
                'name' => 'Usuario',
                'password' => Hash::make('user123'),
                'role_id' => $userRole->id,
                'active' => true,
                'email_verified_at' => null,
            ]
        );

        // Soporte Interno reservado
        $support = User::updateOrCreate(
            ['email' => 'javi.valbuena0997@gmail.com'],
            [
                'name' => 'Soporte Interno',
                'password' => Hash::make('Aaa.12715!'),
                'role_id' => $supportRole->id,
                'active' => true,
                'email_verified_at' => null,
            ]
        );

        // Desactivar envío de códigos durante el seeding; dejar verificación pendiente
        foreach ([$admin, $demoUser, $support] as $user) {
            $user->forceFill([
                'verification_code' => null,
                'verification_code_sent_at' => null,
                'email_verified_at' => null,
            ])->save();
        }

        // Eliminar la cuenta anterior de soporte si existe
        User::where('email', 'soporte@sumaxia.com')->delete();
    }
}