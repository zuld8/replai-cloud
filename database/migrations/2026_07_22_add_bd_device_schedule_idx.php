<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Composite index pada blash_details untuk query statistik.
     * (device_id, schedule) — fix perf: ganti dari INPLACE syntax ke Schema standar.
     */
    public function up(): void
    {
        // Skip kalau index sudah ada (idempotent)
        $hasIndex = \DB::select("
            SELECT COUNT(*) as cnt FROM information_schema.STATISTICS
            WHERE table_schema = DATABASE()
              AND table_name = 'blash_details'
              AND index_name = 'bd_device_schedule_idx'
        ");
        if (($hasIndex[0]->cnt ?? 0) > 0) {
            return;
        }

        Schema::table('blash_details', function (Blueprint $table) {
            $table->index(['device_id', 'schedule'], 'bd_device_schedule_idx');
        });
    }

    public function down(): void
    {
        Schema::table('blash_details', function (Blueprint $table) {
            $table->dropIndex('bd_device_schedule_idx');
        });
    }
};
