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
        Schema::table('whatsapp_devices', function (Blueprint $table) {
            $table->enum('auto_reply_method', ['chatbot', 'ai'])->default('chatbot')->after('status');
            $table->uuid('fine_tunnel_id')->index()->nullable()->after('auto_reply_method');
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
