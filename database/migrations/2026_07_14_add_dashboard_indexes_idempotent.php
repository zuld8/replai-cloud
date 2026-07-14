<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Performance Indexes — Idempotent
 * 
 * Migration ini aman dijalanin berulang karena tiap add dicek dulu.
 * Juga berfungsi sebagai dokumentasi resmi semua index prod di repo
 * → cegah schema-drift staging vs prod.
 *
 * EXPLAIN sebelum migration (prod 2026-07-14):
 *   pesanMasuk: hcd type=range, key=idx_hcd_from_created, rows=22176 ✅
 *   crmMessages: type=range, key=idx_unread_count, rows=627 — tapi Using filesort
 *   interactionAnalysis: type=range, key=idx_created_at ✅
 *   analiss (blash): type=range, key=idx_created_reports ✅
 *
 * Yang perlu ditambah: composite index crmMessages agar hilangkan filesort.
 * Semua index lain sudah ada, migration ini mendokumentasikan + guard kalau belum.
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
            // Sudah ada (dipasang manual di prod) — skip, bukan error
            return;
        }
        DB::statement(
            "ALTER TABLE `{$table}` ADD INDEX `{$index}` {$colsDef}, ALGORITHM=INPLACE, LOCK=NONE"
        );
    }

    public function up(): void
    {
        // ── history_chat_details (2.8M baris) ─────────────────────────────
        // SUDAH ADA di prod: idx_hcd_from_created (from, created_at) — pakai 22k rows ✅
        $this->addIndex('history_chat_details', 'idx_hcd_from_created',      '(`from`, created_at)');
        // SUDAH ADA: idx_hcd_agent_msg (history_chat_id, from, created_at) ✅
        $this->addIndex('history_chat_details', 'idx_hcd_agent_msg',         '(history_chat_id, `from`, created_at)');
        // SUDAH ADA: idx_hcd_chat_created ✅
        $this->addIndex('history_chat_details', 'idx_hcd_chat_created',      '(history_chat_id, created_at)');

        // ── history_chats (559k baris) ─────────────────────────────────────
        // SUDAH ADA: idx_hc_unread_status_lastmsg — tapi crmMessages masih filesort
        // Tambah composite optimal untuk: WHERE unread_count>0 AND status IN (...) ORDER BY last_message_at
        $this->addIndex('history_chats', 'idx_hc_unread_status_lastmsg',     '(unread_count, status, last_message_at)');
        // SUDAH ADA: idx_created_at, idx_status_created, idx_hc_report_biz ✅
        $this->addIndex('history_chats', 'idx_created_at',                   '(created_at)');
        $this->addIndex('history_chats', 'idx_status_created',               '(status, created_at)');
        // Composite crmMessages — baru (menggantikan filesort)
        $this->addIndex('history_chats', 'idx_hc_crm_unread',
            '(business_id, status, unread_count, last_message_at)');

        // ── blash_details (2.3M baris) ─────────────────────────────────────
        // SUDAH ADA: idx_created_reports (created_at, reports) ✅
        $this->addIndex('blash_details', 'idx_created_reports', '(created_at, reports(10))');
        $this->addIndex('blash_details', 'idx_blast_created',   '(blash_whatsapp_id, created_at)');

        // ── blash_whatsapps (relasi parent BlashDetail) ────────────────────
        $this->addIndex('blash_whatsapps', 'bw_merchant_idx', '(merchant_id)');
    }

    public function down(): void
    {
        $toDrop = [
            ['history_chats', 'idx_hc_crm_unread'],        // satu-satunya yg benar-benar baru
            ['blash_whatsapps', 'bw_merchant_idx'],
        ];
        // Yang lain TIDAK di-drop di down() karena sudah ada sebelum migration ini
        foreach ($toDrop as [$table, $index]) {
            if ($this->hasIndex($table, $index)) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
            }
        }
    }
};
