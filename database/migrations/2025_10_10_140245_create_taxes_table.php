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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tax_type', ['iva', 'retencion_fuente', 'retencion_iva', 'renta', 'ica']);
            $table->string('tax_period'); // Ej: 2024-01, 2024-Q1, 2024
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('taxable_base', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 4)->default(0); // Porcentaje del impuesto
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('withholding_amount', 15, 2)->default(0); // Retenciones practicadas
            $table->decimal('balance_to_pay', 15, 2)->default(0);
            $table->decimal('balance_in_favor', 15, 2)->default(0);
            $table->date('due_date');
            $table->enum('status', ['pending', 'filed', 'paid', 'overdue'])->default('pending');
            $table->text('observations')->nullable();
            $table->json('details')->nullable(); // Detalles adicionales en JSON
            $table->timestamps();
            
            $table->index(['tax_type', 'tax_period']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
