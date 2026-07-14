<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Index untuk mempercepat broadcastStatus() query setelah rewrite:
 *   Query baru: 2 query kecil (bukan JOIN besar)
 *   Q1: SELECT id FROM blash_whatsapps WHERE business_id=? ORDER BY created_at DESC LIMIT 5
 *   Q2: SELECT blash_whatsapp_id, COUNT(*) FROM blash_details
 *       WHERE blash_whatsapp_id IN (5 ids) AND type='whatsapp' GROUP BY blash_whatsapp_id
 *
 * Index yg dibutuhkan:
 *   bw_biz_created_idx — supaya Q1 index-only scan, bukan full table scan
 *   bd_bwid_type_idx   — supaya Q2 pakai composite index (blash_whatsapp_id, type, sending_status)
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
        if ($this->hasIndex($table, $index)) return; // idempotent — skip kalau sudah ada
        DB::statement(
            "ALTER TABLE `{$table}` ADD INDEX `{$index}` {$colsDef}, ALGORITHM=INPLACE, LOCK=NONE"
        );
    }

    public function up(): void
    {
        // blash_whatsapps: ambil 5 terbaru per bisnis
        // Kolom: business_id (equality) + created_at (range/sort)
        $this->addIndex('blash_whatsapps', 'bw_biz_created_idx', '(business_id, created_at)');

        // blash_details: agregasi per broadcast + filter type + group by sending_status
        // Kolom: blash_whatsapp_id (IN 5 ids) + type (equality) + sending_status (SUM)
        $this->addIndex('blash_details', 'bd_bwid_type_status_idx', '(blash_whatsapp_id, type, sending_status)');
    }

    public function down(): void
    {
        if ($this->hasIndex('blash_whatsapps', 'bw_biz_created_idx')) {
            DB::statement('ALTER TABLE `blash_whatsapps` DROP INDEX `bw_biz_created_idx`');
        }
        if ($this->hasIndex('blash_details', 'bd_bwid_type_status_idx')) {
            DB::statement('ALTER TABLE `blash_details` DROP INDEX `bd_bwid_type_status_idx`');
        }
    }
};
