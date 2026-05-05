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
            $table->uuid('whatsapp_waba_id')->nullable()->after('livechat_id');
            $table->uuid('instagram_id')->nullable()->after('whatsapp_waba_id');
            $table->uuid('messanger_id')->nullable()->after('instagram_id');
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
