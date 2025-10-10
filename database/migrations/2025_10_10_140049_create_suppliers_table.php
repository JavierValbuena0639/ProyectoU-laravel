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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_type'); // NIT, CC, CE
            $table->string('document_number')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_person')->nullable();
            $table->enum('supplier_type', ['bienes', 'servicios', 'mixto'])->default('mixto');
            $table->boolean('active')->default(true);
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('payment_terms')->default(30); // Días de plazo de pago
            $table->timestamps();
            
            $table->index(['document_number', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
