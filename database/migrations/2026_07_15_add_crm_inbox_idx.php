<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * CRM Inbox Index — Idempotent
 *
 * Tambah compound index untuk query inbox CRM:
 *   WHERE business_id = ? AND is_archived = 0 ORDER BY last_message_at DESC
 *
 * Index idx_crm_main(business_id, status, takeover, last_message_at) tidak include
 * is_archived → MySQL filesort semua chat bisnis tiap scroll halaman berikutnya.
 */
return new class extends Migration
{
    private function hasIndex(string $table, string $index): bool
    {
        return !empty(DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$index]
        ));
    }

    private function addIndex(string $table, string $index, string $colsDef): void
    {
        if ($this->hasIndex($table, $index)) {
            return; // sudah ada (manual atau dari run sebelumnya) — skip
        }
        DB::statement(
            "ALTER TABLE `{$table}` ADD INDEX `{$index}` {$colsDef}, ALGORITHM=INPLACE, LOCK=NONE"
        );
    }

    public function up(): void
    {
        // inbox CRM: WHERE business_id AND is_archived ORDER BY last_message_at DESC
        $this->addIndex(
            'history_chats',
            'idx_hc_biz_arch_lastmsg',
            '(business_id, is_archived, last_message_at)'
        );
    }

    public function down(): void
    {
        if ($this->hasIndex('history_chats', 'idx_hc_biz_arch_lastmsg')) {
            DB::statement('ALTER TABLE `history_chats` DROP INDEX `idx_hc_biz_arch_lastmsg`');
        }
    }
};
