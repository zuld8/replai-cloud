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
        // DB::statement("ALTER TABLE whatsapp_devices MODIFY COLUMN auto_reply_method ENUM('ai', 'chatbot', 'all')");

        Schema::table('whatsapp_devices', function (Blueprint $table) {
            $table->string('auto_reply_method')->default('all')->change();
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
