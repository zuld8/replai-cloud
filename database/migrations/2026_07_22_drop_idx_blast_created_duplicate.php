<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Drop idx_blast_created — duplikat identik dari bd_bwid_created_idx (blash_whatsapp_id, created_at). */
    public function up(): void
    {
        $cnt = DB::selectOne("
            SELECT COUNT(*) as cnt FROM information_schema.STATISTICS
            WHERE table_schema = DATABASE()
              AND table_name = 'blash_details'
              AND index_name = 'idx_blast_created'
        ")->cnt ?? 0;
        if ($cnt > 0) {
            DB::statement('ALTER TABLE blash_details DROP INDEX idx_blast_created');
        }
    }

    public function down(): void
    {
        $cnt = DB::selectOne("
            SELECT COUNT(*) as cnt FROM information_schema.STATISTICS
            WHERE table_schema = DATABASE()
              AND table_name = 'blash_details'
              AND index_name = 'idx_blast_created'
        ")->cnt ?? 0;
        if ($cnt === 0) {
            DB::statement("ALTER TABLE blash_details ADD INDEX idx_blast_created (blash_whatsapp_id, created_at)");
        }
    }
};
