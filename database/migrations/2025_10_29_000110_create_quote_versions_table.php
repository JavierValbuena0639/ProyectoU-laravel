<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');
            $table->unsignedInteger('version');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('email_domain')->index();
            $table->string('change_reason')->nullable();
            $table->json('snapshot');
            $table->timestamps();

            $table->unique(['quote_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_versions');
    }
};