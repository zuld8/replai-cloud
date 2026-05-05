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
            $table->enum('history_ai_chat_option', ['yes', 'no'])->default('no')->after('ai_option'); 
            $table->integer('history_ai_chat')->default(0)->after('history_ai_chat_option');
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
