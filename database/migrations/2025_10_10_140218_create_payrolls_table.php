<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_name');
            $table->string('employee_document');
            $table->string('position');
            $table->date('payroll_period_start');
            $table->date('payroll_period_end');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('bonuses', 15, 2)->default(0);
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('health_contribution', 15, 2)->default(0); // EPS
            $table->decimal('pension_contribution', 15, 2)->default(0); // Pensión
            $table->decimal('arl_contribution', 15, 2)->default(0); // ARL
            $table->decimal('income_tax', 15, 2)->default(0); // Retención en la fuente
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2);
            $table->decimal('severance_pay', 15, 2)->default(0); // Cesantías
            $table->decimal('severance_interest', 15, 2)->default(0); // Intereses cesantías
            $table->decimal('bonus_payment', 15, 2)->default(0); // Prima
            $table->decimal('vacation_pay', 15, 2)->default(0); // Vacaciones
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->timestamps();
            
            $table->index(['payroll_period_start', 'payroll_period_end']);
            $table->index(['employee_document', 'payroll_period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
