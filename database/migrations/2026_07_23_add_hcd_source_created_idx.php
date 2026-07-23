<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Index (source, created_at) di history_chat_details — untuk dashboard admin:
 *   - AI top per bisnis: WHERE source='bot' AND created_at BETWEEN ...
 *   - AI usage automation: SUM CASE source IN ('bot','flow') + created_at range
 * ALGORITHM=INPLACE, LOCK=NONE → aman jalan live, tidak blocking.
 */
return new class extends Migration
{
    private function hasIndex(string $table, string $index): bool
    {
        $result = DB::select(
            "SELECT 1 FROM information_schema.statistics
             WHERE table_schema = DATABASE()
             AND table_name = ? AND index_name = ? LIMIT 1",
            [$table, $index]
        );
        return !empty($result);
    }

    public function up(): void
    {
        if (!$this->hasIndex('history_chat_details', 'idx_hcd_source_created')) {
            DB::statement(
                'ALTER TABLE `history_chat_details`
                 ADD INDEX `idx_hcd_source_created` (`source`, `created_at`),
                 ALGORITHM=INPLACE, LOCK=NONE'
            );
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('history_chat_details', 'idx_hcd_source_created')) {
            DB::statement('ALTER TABLE `history_chat_details` DROP INDEX `idx_hcd_source_created`');
        }
    }
};
