<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BSUID Support — Meta Username Rollout (Deadline: Juni 2026)
     * BSUIDs mulai muncul di webhook sejak 31 Maret 2026.
     *
     * Ketika user WhatsApp aktifkan username, nomor telepon mereka
     * tidak lagi tersedia di webhook payload. BSUID (Business-Scoped
     * User ID) digunakan sebagai identifier pengganti.
     */
    public function up(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('history_chats', 'bsuid')) {
                $table->string('bsuid', 200)->nullable()->after('from_number')->index();
            }
            if (!Schema::hasColumn('history_chats', 'wa_username')) {
                $table->string('wa_username', 100)->nullable()->after('bsuid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            $table->dropColumn(['bsuid', 'wa_username']);
        });
    }
};

