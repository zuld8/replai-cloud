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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('whatsapp_session')->nullable();
            $table->dropColumn('whatsapp_key')->nullable();
            $table->enum('whatsapp_sender_notif', ['sequence', 'spin', 'random'])->default('sequence')->after('mail_encryption');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
