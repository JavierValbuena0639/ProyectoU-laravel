<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CleanupRandomUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // No ejecutar en producción salvo habilitación explícita
        if (app()->environment('production') && !filter_var(env('ALLOW_DEMO_SEED_IN_PROD', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }
        if (!filter_var(env('DEMO_SEED_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }
        // Mantener solo los usuarios demo definidos y eliminar el resto
        $keepEmails = ['admin@sumaxia.com', 'user@sumaxia.com'];

        $randomUsers = \App\Models\User::whereNotIn('email', $keepEmails)->get();

        foreach ($randomUsers as $user) {
            // Eliminar relaciones dependientes simples
            \App\Models\Invoice::where('user_id', $user->id)->delete();
            \App\Models\Payroll::where('user_id', $user->id)->delete();
            \App\Models\Transaction::where('user_id', $user->id)->delete();
            \App\Models\Audit::where('user_id', $user->id)->delete();
            \App\Models\AdminUserAccount::where('user_id', $user->id)->delete();

            // Finalmente eliminar el usuario
            $user->delete();
        }
    }
}