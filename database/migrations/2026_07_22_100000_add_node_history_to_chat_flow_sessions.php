<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom node_history ke chat_flow_sessions.
     * Idempotent — aman dijalanin ulang.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('chat_flow_sessions', 'node_history')) {
            Schema::table('chat_flow_sessions', function (Blueprint $table) {
                $table->json('node_history')->nullable()->after('current_node_id')
                      ->comment('Stack UUID node yang sudah dilewati — untuk back_previous');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chat_flow_sessions', 'node_history')) {
            Schema::table('chat_flow_sessions', function (Blueprint $table) {
                $table->dropColumn('node_history');
            });
        }
    }
};
