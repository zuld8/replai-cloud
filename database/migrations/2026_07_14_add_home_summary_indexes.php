<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Index untuk mempercepat home() $summary + warmup blast queries:
 *
 * Query baru (setelah rewrite):
 *   Q1: SELECT id, use FROM blash_whatsapps WHERE business_id=? (sudah ada bw_biz_created_idx atau idx_bw_biz_waba)
 *   Q2: COUNT(*) FROM blash_details WHERE blash_whatsapp_id IN (...) AND created_at BETWEEN ...
 *   Q3: SUM(reports IS NULL) FROM blash_details WHERE blash_whatsapp_id IN (...) AND created_at >= ...
 *
 * Index Q2+Q3: (blash_whatsapp_id, created_at) — pakai blash_whatsapp_id untuk IN lookup + created_at untuk range
 * Index Q1:   (business_id, use) — optional tapi membantu kalau blash_whatsapps besar
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
        if ($this->hasIndex($table, $index)) return;
        DB::statement(
            "ALTER TABLE `{$table}` ADD INDEX `{$index}` {$colsDef}, ALGORITHM=INPLACE, LOCK=NONE"
        );
    }

    public function up(): void
    {
        // blash_whatsapps: filter business_id + use (untuk Q1 dan fallback whereHas)
        $this->addIndex('blash_whatsapps', 'bw_biz_use_idx', '(business_id, `use`)');

        // blash_details: (blash_whatsapp_id, created_at) untuk Q2+Q3
        // IN lookup pada blash_whatsapp_id (sudah ada index?), + created_at range scan
        $this->addIndex('blash_details', 'bd_bwid_created_idx', '(blash_whatsapp_id, created_at)');
    }

    public function down(): void
    {
        if ($this->hasIndex('blash_whatsapps', 'bw_biz_use_idx')) {
            DB::statement('ALTER TABLE `blash_whatsapps` DROP INDEX `bw_biz_use_idx`');
        }
        if ($this->hasIndex('blash_details', 'bd_bwid_created_idx')) {
            DB::statement('ALTER TABLE `blash_details` DROP INDEX `bd_bwid_created_idx`');
        }
    }
};
