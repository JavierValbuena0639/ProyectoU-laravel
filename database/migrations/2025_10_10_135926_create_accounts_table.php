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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Código PUC
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['activo', 'pasivo', 'patrimonio', 'ingreso', 'gasto', 'costo']);
            $table->enum('nature', ['debito', 'credito']); // Naturaleza de la cuenta
            $table->unsignedBigInteger('parent_id')->nullable(); // Para cuentas padre
            $table->integer('level'); // Nivel en el PUC (1, 2, 3, 4, etc.)
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('accepts_movements')->default(true); // Si acepta movimientos directos
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->index(['code', 'type', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
