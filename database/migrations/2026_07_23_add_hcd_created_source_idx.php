<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tambah index idx_hcd_created_source (created_at, source) di history_chat_details.
 *
 * Mengapa created_at DI DEPAN:
 *  - Query admin dashboard semua pakai range filter created_at (bulan/7hari).
 *  - Dengan created_at di depan, MySQL bisa nyempitkan ke rentang kecil dulu
 *    (misal: 1 bulan = ~200-400k rows dari 2.8jt) sebelum filter source.
 *  - Berbeda dgn idx_hcd_source_created (source, created_at) yg hanya berguna
 *    kalau ada WHERE source='...' equality (ai_top query ✓, tapi ai_usage ✗).
 *
 * Query yang terbantu:
 *  - admin_ai_usage: WHERE created_at BETWEEN + from='device' + SUM(CASE source)
 *  - admin_credit_ai_total: WHERE created_at BETWEEN + SUM(credit_using)
 *  - admin_credit_ai (response-ai): WHERE created_at BETWEEN + GROUP BY date
 * ALGORITHM=INPLACE, LOCK=NONE → aman live tanpa blocking.
 */
return new class extends Migration
{
    private function hasIndex(string $table, string $index): bool
    {
        $r = DB::select(
            "SELECT 1 FROM information_schema.statistics
             WHERE table_schema=DATABASE() AND table_name=? AND index_name=? LIMIT 1",
            [$table, $index]
        );
        return !empty($r);
    }

    public function up(): void
    {
        if (!$this->hasIndex('history_chat_details', 'idx_hcd_created_source')) {
            DB::statement(
                'ALTER TABLE `history_chat_details`
                 ADD INDEX `idx_hcd_created_source` (`created_at`, `source`),
                 ALGORITHM=INPLACE, LOCK=NONE'
            );
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('history_chat_details', 'idx_hcd_created_source')) {
            DB::statement('ALTER TABLE `history_chat_details` DROP INDEX `idx_hcd_created_source`');
        }
    }
};
