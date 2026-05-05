<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('whatsapp_key_accounts', function (Blueprint $table) {
            $table->string('auto_reply_method')->default('chatbot')->change();
        });

        //  DB::statement("ALTER TABLE whatsapp_key_accounts MODIFY COLUMN `auto_reply_method` ENUM('chatbot', 'ai','all') default 'chatbot'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
