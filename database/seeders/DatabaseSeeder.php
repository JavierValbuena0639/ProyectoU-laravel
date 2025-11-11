<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Sembrar roles base primero para mantener consistencia (incluye soporte_interno)
        $this->call([
            RoleSeeder::class,
            AdminUserCredentialsSeeder::class,
            TenantDemoSeeder::class,
        ]);
    }
}
