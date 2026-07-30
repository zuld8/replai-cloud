<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_chat_details', function (Blueprint $t) {
            // Status pengiriman: sent/delivered/read/failed (dari webhook Meta)
            if (!Schema::hasColumn('history_chat_details', 'delivery_status')) {
                $t->string('delivery_status', 20)->nullable()->after('is_read');
            }
            // Detail error kalau delivery_status = failed (JSON: code, title, message)
            if (!Schema::hasColumn('history_chat_details', 'delivery_error')) {
                $t->text('delivery_error')->nullable()->after('delivery_status');
            }
        });

        // Index messageid kalau belum ada (untuk lookup cepat by wamid)
        try {
            // Cek apakah index sudah ada
            $hasIndex = \Illuminate\Support\Facades\DB::select(
                "SHOW INDEX FROM history_chat_details WHERE Key_name = 'idx_hcd_messageid'"
            );
            if (empty($hasIndex)) {
                Schema::table('history_chat_details', function (Blueprint $t) {
                    $t->index('messageid', 'idx_hcd_messageid');
                });
            }
        } catch (\Throwable $e) {
            // Index mungkin sudah ada dengan nama berbeda — tidak masalah
        }
    }

    public function down(): void
    {
        Schema::table('history_chat_details', function (Blueprint $t) {
            $t->dropColumn(['delivery_status', 'delivery_error']);
        });
    }
};
