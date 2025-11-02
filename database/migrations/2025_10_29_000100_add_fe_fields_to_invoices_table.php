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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('fe_status')->nullable()->after('status');
            $table->string('fe_cufe')->nullable()->after('fe_status');
            $table->string('fe_uuid')->nullable()->after('fe_cufe');
            $table->string('fe_xml_path')->nullable()->after('fe_uuid');
            $table->string('fe_request_path')->nullable()->after('fe_xml_path');
            $table->string('fe_response_path')->nullable()->after('fe_request_path');
            $table->string('fe_response_code')->nullable()->after('fe_response_path');
            $table->text('fe_response_message')->nullable()->after('fe_response_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'fe_status',
                'fe_cufe',
                'fe_uuid',
                'fe_xml_path',
                'fe_request_path',
                'fe_response_path',
                'fe_response_code',
                'fe_response_message',
            ]);
        });
    }
};