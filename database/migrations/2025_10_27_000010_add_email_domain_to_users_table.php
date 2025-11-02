<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_domain')) {
                $table->string('email_domain')->nullable()->index()->after('email');
            }
        });

        // Backfill email_domain for existing users in a driver-agnostic way
        try {
            DB::table('users')
                ->select(['id', 'email'])
                ->orderBy('id')
                ->chunkById(500, function ($users) {
                    foreach ($users as $u) {
                        $email = (string) ($u->email ?? '');
                        $parts = explode('@', $email);
                        $domain = $parts[1] ?? null;
                        if ($domain) {
                            DB::table('users')->where('id', $u->id)->update(['email_domain' => strtolower($domain)]);
                        }
                    }
                });
        } catch (\Throwable $e) {
            // ignore backfill errors to avoid blocking migrations
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_domain')) {
                $table->dropIndex(['email_domain']);
                $table->dropColumn('email_domain');
            }
        });
    }
};