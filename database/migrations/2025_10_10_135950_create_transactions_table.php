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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique(); // Número de comprobante
            $table->enum('voucher_type', ['ingreso', 'egreso', 'diario', 'ajuste']); // Tipo de comprobante
            $table->date('transaction_date');
            $table->text('description');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->string('reference')->nullable(); // Referencia externa (factura, etc.)
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->timestamps();
            
            $table->index(['transaction_date', 'voucher_type', 'status']);
            $table->index(['account_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
