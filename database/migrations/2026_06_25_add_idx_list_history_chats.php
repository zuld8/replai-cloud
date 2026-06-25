<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Composite index: business_id filter + is_archived filter + last_message_at ORDER
        // Eliminates full-table filesort on 303k-row CRM chat list
        // IF NOT EXISTS: safe to re-run (MySQL 8+)
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_list
            ON history_chats (business_id, is_archived, last_message_at)
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_list ON history_chats');
    }
};
