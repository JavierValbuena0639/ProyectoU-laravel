<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios existentes
        $admin = \App\Models\User::where('email', 'admin@sumaxia.com')->first();
        $user = \App\Models\User::where('email', 'user@sumaxia.com')->first();

        if (!$admin || !$user) {
            // Asegurar que existan usuarios base
            $this->call(UserSeeder::class);
            $admin = \App\Models\User::where('email', 'admin@sumaxia.com')->first();
            $user = \App\Models\User::where('email', 'user@sumaxia.com')->first();
        }

        // Crear proveedor de muestra (usar columnas reales de la migración)
        DB::table('suppliers')->updateOrInsert(
            [
                'document_type' => 'NIT',
                'document_number' => '900123456-7',
            ],
            [
                'name' => 'Proveedor Ejemplo S.A.S.',
                'email' => 'contacto@proveedor-ejemplo.com',
                'phone' => '6011234567',
                'address' => 'Cra 7 #123-45',
                'city' => 'Bogotá',
                'contact_person' => 'María López',
                'supplier_type' => 'servicios',
                'active' => true,
                'credit_limit' => 10000000,
                'payment_terms' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $supplier = DB::table('suppliers')->where('document_number', '900123456-7')->first();

        // Crear factura de muestra (usar columnas reales de la migración)
        DB::table('invoices')->updateOrInsert(
            [
                'invoice_number' => 'FAC-0001',
            ],
            [
                'user_id' => $user->id,
                'client_name' => 'Cliente Demo S.A.S.',
                'client_document' => '900987654-3',
                'client_email' => 'tesoreria@cliente-demo.com',
                'client_address' => 'Av 10 #45-67, Medellín',
                'invoice_date' => now()->subDays(2)->toDateString(),
                'due_date' => now()->addDays(28)->toDateString(),
                'subtotal' => 2500000,
                'tax_amount' => 475000,
                'retention_amount' => 0,
                'total_amount' => 2975000,
                'paid_amount' => 0,
                'status' => 'sent',
                'notes' => 'Factura de muestra creada por seeder',
                'items' => json_encode([
                    ['description' => 'Servicio de consultoría', 'quantity' => 1, 'price' => 2500000]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $invoice = DB::table('invoices')->where('invoice_number', 'FAC-0001')->first();

        // Crear nómina de muestra (usar columnas reales de la migración)
        DB::table('payrolls')->updateOrInsert(
            [
                'employee_document' => 'CC-1234567890',
                'payroll_period_start' => now()->startOfMonth()->toDateString(),
                'payroll_period_end' => now()->endOfMonth()->toDateString(),
            ],
            [
                'employee_name' => 'Juan Pérez',
                'position' => 'Analista Contable',
                'user_id' => $admin->id,
                'basic_salary' => 3500000,
                'overtime_hours' => 5,
                'overtime_amount' => 75000,
                'bonuses' => 200000,
                'gross_salary' => 3775000,
                'health_contribution' => 151000,
                'pension_contribution' => 151000,
                'arl_contribution' => 50000,
                'income_tax' => 0,
                'other_deductions' => 0,
                'net_salary' => 3473000,
                'severance_pay' => 0,
                'severance_interest' => 0,
                'bonus_payment' => 0,
                'vacation_pay' => 0,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $payroll = DB::table('payrolls')->where('employee_document', 'CC-1234567890')->first();

        // Auditar eventos de muestra
        \App\Models\Audit::firstOrCreate([
            'user_id' => $user->id,
            'event' => 'invoice_created',
            'auditable_type' => \App\Models\Invoice::class,
            'auditable_id' => $invoice->id ?? null,
            'description' => 'Nueva factura creada — FAC-0001 por COP$2.975.000',
        ], [
            'old_values' => [],
            'new_values' => ['invoice_number' => 'FAC-0001', 'total_amount' => 2975000],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder',
            'url' => '/invoicing/invoices',
        ]);

        \App\Models\Audit::firstOrCreate([
            'user_id' => $admin->id,
            'event' => 'user_registered',
            'auditable_type' => \App\Models\User::class,
            'auditable_id' => $user->id,
            'description' => 'Nuevo usuario registrado — Usuario Demo',
        ], [
            'old_values' => [],
            'new_values' => ['email' => $user->email],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder',
            'url' => '/admin/users',
        ]);

        \App\Models\Audit::firstOrCreate([
            'user_id' => $admin->id,
            'event' => 'payroll_processed',
            'auditable_type' => \App\Models\Payroll::class,
            'auditable_id' => $payroll->id ?? null,
            'description' => 'Nómina procesada — Juan Pérez por COP$3.473.000',
        ], [
            'old_values' => [],
            'new_values' => ['net_salary' => 3473000],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder',
            'url' => '/payroll',
        ]);
    }
}