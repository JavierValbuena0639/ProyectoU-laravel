<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('email_domain')->index();
            $table->string('client_name');
            $table->string('client_document');
            $table->string('client_email')->nullable();
            $table->string('client_company')->nullable();
            $table->text('client_address')->nullable();
            $table->date('issue_date');
            $table->date('valid_until');
            $table->text('project_description');
            $table->json('items');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'converted'])->default('draft');
            $table->timestamps();

            $table->index(['issue_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};