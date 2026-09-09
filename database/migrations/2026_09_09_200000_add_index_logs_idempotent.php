<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Idempotent: hanya buat index kalau belum ada
        $exists = collect(DB::select("SHOW INDEX FROM logs"))
            ->pluck('Key_name')
            ->contains('idx_logs_biz_type_created');

        if (!$exists) {
            Schema::table('logs', function (Blueprint $t) {
                $t->index(['business_id', 'type', 'created_at'], 'idx_logs_biz_type_created');
            });
        }
    }

    public function down(): void
    {
        // Cek dulu sebelum drop, agar aman dijalankan berkali-kali
        $exists = collect(DB::select("SHOW INDEX FROM logs"))
            ->pluck('Key_name')
            ->contains('idx_logs_biz_type_created');

        if ($exists) {
            Schema::table('logs', function (Blueprint $t) {
                $t->dropIndex('idx_logs_biz_type_created');
            });
        }
    }
};
