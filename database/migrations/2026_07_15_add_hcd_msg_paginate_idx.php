<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Idempotent — aman dijalankan berkali-kali
    private function hasIndex(string $table, string $index): bool
    {
        $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return !empty($result);
    }

    public function up(): void
    {
        // Index untuk query getMessages:
        // WHERE history_chat_id=? AND deleted_at IS NULL ORDER BY created_at DESC, id DESC LIMIT 20
        // → seek by (history_chat_id, deleted_at) + sorted scan created_at,id → no filesort
        if (!$this->hasIndex('history_chat_details', 'idx_hcd_msg_paginate')) {
            DB::statement(
                'ALTER TABLE `history_chat_details`
                 ADD INDEX `idx_hcd_msg_paginate` (`history_chat_id`, `deleted_at`, `created_at`, `id`)
                 ALGORITHM=INPLACE LOCK=NONE'
            );
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('history_chat_details', 'idx_hcd_msg_paginate')) {
            Schema::table('history_chat_details', function (Blueprint $table) {
                $table->dropIndex('idx_hcd_msg_paginate');
            });
        }
    }
};
