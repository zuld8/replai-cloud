<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
//        $this->cleanDuplicates();


//        Schema::table('history_chats', function (Blueprint $table) {
  //          $table->index(['device_id', 'from_number', 'type'], 'idx_device_from_type');
    //        $table->index(['from_number', 'jid_number'], 'idx_numbers');
      //      $table->index(['status', 'takeover'], 'idx_status_takeover');
      //      $table->index('created_at', 'idx_created_at');
      //  });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            $table->dropUnique('unique_chat_session');
            $table->dropIndex('idx_device_from_type');
            $table->dropIndex('idx_numbers');
            $table->dropIndex('idx_status_takeover');
            $table->dropIndex('idx_created_at');
        });
    }

    /**
     * Clean duplicate history chats before adding unique constraint
     */
    private function cleanDuplicates(): void
    {
        $duplicateCount = DB::select("
            SELECT COUNT(*) as total FROM (
                SELECT h1.id
                FROM history_chats h1
                INNER JOIN history_chats h2 
                WHERE h1.id > h2.id 
                  AND h1.device_id <=> h2.device_id
                  AND h1.from_number = h2.from_number
                  AND h1.type = h2.type
                  AND h1.from = h2.from
                  AND h1.livechat_id <=> h2.livechat_id
                  AND h1.whatsapp_waba_id <=> h2.whatsapp_waba_id
                  AND h1.telegram_id <=> h2.telegram_id
                  AND h1.instagram_id <=> h2.instagram_id
                  AND h1.messanger_id <=> h2.messanger_id
            ) as duplicates
        ")[0]->total ?? 0;

        if ($duplicateCount > 0) {
            Log::info("Found {$duplicateCount} duplicate history chats, cleaning...");

            // Delete duplicates
            DB::statement("
                DELETE h1 FROM history_chats h1
                INNER JOIN history_chats h2 
                WHERE h1.id > h2.id 
                  AND h1.device_id <=> h2.device_id
                  AND h1.from_number = h2.from_number
                  AND h1.type = h2.type
                  AND h1.from = h2.from
                  AND h1.livechat_id <=> h2.livechat_id
                  AND h1.whatsapp_waba_id <=> h2.whatsapp_waba_id
                  AND h1.telegram_id <=> h2.telegram_id
                  AND h1.instagram_id <=> h2.instagram_id
                  AND h1.messanger_id <=> h2.messanger_id
            ");

            Log::info("Successfully cleaned {$duplicateCount} duplicate history chats");
        } else {
            Log::info("No duplicate history chats found");
        }
    }
};
