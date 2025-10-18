<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fe_resolutions', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 10);
            $table->unsignedBigInteger('number_from');
            $table->unsignedBigInteger('number_to');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fe_resolutions');
    }
};