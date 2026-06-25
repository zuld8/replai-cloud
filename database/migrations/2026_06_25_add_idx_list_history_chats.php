<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        $exists = DB::select("SHOW INDEX FROM history_chats WHERE Key_name = 'idx_list'");
        if (!empty($exists)) { return; }
        DB::statement('CREATE INDEX idx_list ON history_chats (business_id, is_archived, last_message_at)');
    }
    public function down(): void {
        $exists = DB::select("SHOW INDEX FROM history_chats WHERE Key_name = 'idx_list'");
        if (!empty($exists)) { DB::statement('DROP INDEX idx_list ON history_chats'); }
    }
};
