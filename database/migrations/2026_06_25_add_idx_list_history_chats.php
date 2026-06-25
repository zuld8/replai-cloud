<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('history_chats', function (Blueprint $table) {
            try { $table->dropIndex('idx_list'); } catch (\Throwable $e) {}
            $table->index(['business_id', 'is_archived', 'last_message_at'], 'idx_list');
        });
    }
    public function down(): void {
        Schema::table('history_chats', function (Blueprint $table) {
            try { $table->dropIndex('idx_list'); } catch (\Throwable $e) {}
        });
    }
};
