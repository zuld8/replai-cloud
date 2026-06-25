<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            // Waktu pesan masuk terakhir dari pelanggan — untuk WABA 24h session chip
            // HANYA di-update saat from='user' (inbound), TIDAK oleh pesan agent/outbound
            $table->timestamp('last_inbound_at')->nullable()->after('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            $table->dropColumn('last_inbound_at');
        });
    }
};
