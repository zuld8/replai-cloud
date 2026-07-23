<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kolom 'type' ke meta_accounts.
     * Default 'waba' — semua record lama adalah WABA accounts.
     * Nilai valid: waba, instagram, messenger
     */
    public function up(): void
    {
        if (!Schema::hasColumn('meta_accounts', 'type')) {
            Schema::table('meta_accounts', function (Blueprint $table) {
                $table->string('type', 50)->default('waba')->after('business_id');
            });
        }

        // Backfill: semua record lama = waba (default sudah ngisi, tapi eksplisit)
        DB::statement("UPDATE meta_accounts SET type = 'waba' WHERE type IS NULL OR type = ''");
    }

    public function down(): void
    {
        Schema::table('meta_accounts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
