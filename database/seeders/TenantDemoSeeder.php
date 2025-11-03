<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\VerificationCodeMail;
use App\Models\User;

class TenantDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Guardas de entorno para evitar truncados y datos demo no intencionados
        if (app()->environment('production') && !filter_var(env('ALLOW_DEMO_SEED_IN_PROD', false), FILTER_VALIDATE_BOOLEAN)) {
            // No ejecutar en producción a menos que se habilite explícitamente
            return;
        }
        if (!filter_var(env('DEMO_SEED_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
            // Permite desactivar el seeding demo en cualquier entorno
            return;
        }
        // Limpieza de tablas demo (conservar usuarios para mantener fundador/admin)
        Schema::disableForeignKeyConstraints();
        DB::table('admin_user_accounts')->truncate();
        DB::table('audits')->truncate();
        DB::table('transactions')->truncate();
        DB::table('payrolls')->truncate();
        DB::table('invoices')->truncate();
        DB::table('suppliers')->truncate();
        DB::table('accounts')->truncate();
        // NO truncar usuarios ni roles para no perder al fundador/admin
        Schema::enableForeignKeyConstraints();

        // Roles mínimos (upsert seguro)
        DB::table('roles')->upsert([
            ['id' => 1, 'name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Admin role', 'active' => true],
            ['id' => 2, 'name' => 'user', 'display_name' => 'Usuario', 'description' => 'User role', 'active' => true],
            ['id' => 3, 'name' => 'accountant', 'display_name' => 'Contador', 'description' => 'Accountant role', 'active' => true],
        ], ['id'], ['name','display_name','description','active']);

        $domain = 'sumaxia.com';

        // Crear 5 usuarios en la cuenta demo (todos rol usuario, verificación pendiente)
        $createdUsers = [];
        for ($i = 1; $i <= 5; $i++) {
            $email = "user{$i}@{$domain}";
            // Evitar duplicar si ya existe
            $exists = DB::table('users')->where('email', $email)->first();
            if ($exists) {
                $createdUsers[$i] = (int) $exists->id;
                // Asegurar que tenga email_domain
                if (empty($exists->email_domain)) {
                    DB::table('users')->where('id', $exists->id)->update(['email_domain' => $domain]);
                }
                continue;
            }
            $userId = DB::table('users')->insertGetId([
                'name' => "Usuario {$i}",
                'email' => $email,
                'email_domain' => $domain,
                'password' => Hash::make('demo123'),
                'role_id' => 2, // todos usuarios; único admin es admin@sumaxia.com
                'active' => true,
                'email_verified_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $createdUsers[$i] = $userId;

            // Generar y enviar código de verificación
            try {
                $userModel = User::find($userId);
                // Desactivado: no enviar correo ni establecer código durante el seeding
                $userModel->forceFill([
                    'verification_code' => null,
                    'verification_code_sent_at' => null,
                    'email_verified_at' => null,
                ])->save();
            } catch (\Throwable $e) {
                // No bloquear el seeding si falla el envío
            }
        }

        // Vincular admin con los usuarios demo
        $adminId = (int) DB::table('users')->where('email', 'admin@sumaxia.com')->value('id');
        if (!$adminId && isset($createdUsers[1])) {
            $adminId = (int) $createdUsers[1]; // fallback
        }
        foreach ($createdUsers as $uid) {
            DB::table('admin_user_accounts')->insert([
                'admin_id' => $adminId,
                'user_id' => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Crear cuentas contables base (asegurando dominio de servicio)
        // Caja general
        $existingCash = DB::table('accounts')->where('code', '110505')->first();
        if ($existingCash) {
            DB::table('accounts')->where('code', '110505')->update([
                'name' => 'Caja general',
                'description' => 'Efectivo en caja',
                'type' => 'activo',
                'nature' => 'debito',
                'level' => 4,
                'balance' => $existingCash->balance ?? 0,
                'accepts_movements' => true,
                'active' => true,
                'service_domain' => $domain,
                'updated_at' => now(),
            ]);
            $accountCashId = $existingCash->id;
        } else {
            $accountCashId = DB::table('accounts')->insertGetId([
                'code' => '110505',
                'name' => 'Caja general',
                'description' => 'Efectivo en caja',
                'type' => 'activo',
                'nature' => 'debito',
                'level' => 4,
                'balance' => 0,
                'accepts_movements' => true,
                'active' => true,
                'service_domain' => $domain,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ingresos por servicios
        $existingRevenue = DB::table('accounts')->where('code', '4135')->first();
        if ($existingRevenue) {
            DB::table('accounts')->where('code', '4135')->update([
                'name' => 'Ingresos por servicios',
                'description' => 'Ventas de servicios',
                'type' => 'ingreso',
                'nature' => 'credito',
                'level' => 3,
                'balance' => $existingRevenue->balance ?? 0,
                'accepts_movements' => true,
                'active' => true,
                'service_domain' => $domain,
                'updated_at' => now(),
            ]);
            $accountRevenueId = $existingRevenue->id;
        } else {
            $accountRevenueId = DB::table('accounts')->insertGetId([
                'code' => '4135',
                'name' => 'Ingresos por servicios',
                'description' => 'Ventas de servicios',
                'type' => 'ingreso',
                'nature' => 'credito',
                'level' => 3,
                'balance' => 0,
                'accepts_movements' => true,
                'active' => true,
                'service_domain' => $domain,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Generar proveedores aleatorios
        $cities = ['Bogotá', 'Medellín', 'Cali', 'Barranquilla'];
        $suppliers = [];
        for ($i = 1; $i <= 5; $i++) {
            $suppliers[$i] = DB::table('suppliers')->insertGetId([
                'name' => "Proveedor {$i} S.A.S.",
                'document_type' => 'NIT',
                'document_number' => "9001{$i}2345-{$i}",
                'email' => "proveedor{$i}@{$domain}",
                'phone' => '601-555-'.str_pad((string)($i*11), 4, '0', STR_PAD_LEFT),
                'address' => 'Calle 123 #45-67',
                'city' => $cities[array_rand($cities)],
                'contact_person' => 'Contacto '.$i,
                'supplier_type' => ['bienes','servicios','mixto'][array_rand(['bienes','servicios','mixto'])],
                'active' => true,
                'credit_limit' => rand(10,50)*1000000,
                'payment_terms' => [15,30,45,60][array_rand([15,30,45,60])],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Generar facturas aleatorias para usuarios
        for ($i = 1; $i <= 15; $i++) {
            $userIdx = array_rand($createdUsers);
            $userId = $createdUsers[$userIdx];
            $invoiceDate = Carbon::now()->subDays(rand(0, 60));
            $dueDate = (clone $invoiceDate)->addDays([15,30,45][array_rand([15,30,45])]);
            $subtotal = rand(5, 25) * 100000;
            $tax = round($subtotal * 0.19, 2);
            $retention = round($subtotal * [0, 0.025, 0.04][array_rand([0, 0.025, 0.04])], 2);
            $total = $subtotal + $tax - $retention;
            DB::table('invoices')->insert([
                'invoice_number' => 'FAC-'.str_pad((string)$i, 4, '0', STR_PAD_LEFT),
                'user_id' => $userId,
                'client_name' => 'Cliente '.$i.' S.A.S.',
                'client_document' => 'NIT 900'.rand(100000,999999).'-'.rand(1,9),
                'client_email' => "cliente{$i}@{$domain}",
                'client_address' => 'Carrera 7 #45-10',
                'invoice_date' => $invoiceDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'retention_amount' => $retention,
                'total_amount' => $total,
                'paid_amount' => 0,
                'status' => ['draft','sent','paid','overdue'][array_rand(['draft','sent','paid','overdue'])],
                'notes' => 'Factura generada automáticamente para demo',
                'items' => json_encode([
                    ['description' => 'Servicio profesional', 'quantity' => 1, 'unit_price' => $subtotal],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Generar nóminas aleatorias
        for ($i = 1; $i <= 10; $i++) {
            $userIdx = array_rand($createdUsers);
            $userId = $createdUsers[$userIdx];
            $start = Carbon::now()->firstOfMonth()->subMonths(rand(0,3));
            $end = (clone $start)->endOfMonth();
            $basic = rand(2, 6) * 1000000;
            $overtimeHours = rand(0, 10);
            $overtimeAmount = round($overtimeHours * 15000, 2);
            $bonuses = rand(0,3) * 200000;
            $gross = $basic + $overtimeAmount + $bonuses;
            $health = round($gross * 0.04, 2);
            $pension = round($gross * 0.04, 2);
            $arl = round($gross * 0.005, 2);
            $incomeTax = round(max(0, ($gross - 4000000)) * 0.02, 2);
            $other = rand(0,2) * 50000;
            $net = $gross - ($health + $pension + $arl + $incomeTax + $other);
            DB::table('payrolls')->insert([
                'user_id' => $userId,
                'employee_name' => 'Empleado '.$i,
                'employee_document' => 'CC '.rand(1000000000, 1999999999),
                'position' => ['Analista','Desarrollador','Contador','Gerente'][array_rand(['Analista','Desarrollador','Contador','Gerente'])],
                'payroll_period_start' => $start->toDateString(),
                'payroll_period_end' => $end->toDateString(),
                'basic_salary' => $basic,
                'overtime_hours' => $overtimeHours,
                'overtime_amount' => $overtimeAmount,
                'bonuses' => $bonuses,
                'gross_salary' => $gross,
                'health_contribution' => $health,
                'pension_contribution' => $pension,
                'arl_contribution' => $arl,
                'income_tax' => $incomeTax,
                'other_deductions' => $other,
                'net_salary' => $net,
                'severance_pay' => round($gross * 0.0833, 2),
                'severance_interest' => round($gross * 0.01, 2),
                'bonus_payment' => round($basic * 0.5, 2),
                'vacation_pay' => round($basic * 0.0417, 2),
                'status' => ['draft','approved','paid'][array_rand(['draft','approved','paid'])],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Generar transacciones contables básicas
        for ($i = 1; $i <= 20; $i++) {
            $userIdx = array_rand($createdUsers);
            $userId = $createdUsers[$userIdx];
            $date = Carbon::now()->subDays(rand(1, 90));
            $type = ['ingreso','egreso','diario','ajuste'][array_rand(['ingreso','egreso','diario','ajuste'])];
            $debit = rand(1, 30) * 50000;
            $credit = $type === 'ingreso' ? 0 : rand(1, 30) * 50000;
            DB::table('transactions')->insert([
                'voucher_number' => 'VC-'.Str::upper(Str::random(6)),
                'voucher_type' => $type,
                'transaction_date' => $date->toDateString(),
                'description' => 'Movimiento contable de demo',
                'account_id' => ($type === 'ingreso' ? $accountRevenueId : $accountCashId),
                'user_id' => $userId,
                'debit_amount' => $debit,
                'credit_amount' => $credit,
                'reference' => 'REF-'.Str::upper(Str::random(4)),
                'status' => ['draft','posted','cancelled'][array_rand(['draft','posted','cancelled'])],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Auditoría ligera
        foreach ($createdUsers as $uid) {
            DB::table('audits')->insert([
                'user_id' => $uid,
                'event' => 'user_registered',
                'auditable_type' => 'User',
                'auditable_id' => $uid,
                'old_values' => null,
                'new_values' => json_encode(['email_domain' => $domain]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/1.0',
                'url' => 'http://sumaxia.com/register',
                'description' => 'Usuario creado en entorno demo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Código de verificación diario ya establecido al crear usuarios demo arriba
    }
}