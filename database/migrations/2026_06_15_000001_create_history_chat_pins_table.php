<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_chat_pins', function (Blueprint $table) {
            $table->uuid('history_chat_id');
            $table->uuid('user_id');
            $table->timestamps();
            $table->unique(['history_chat_id', 'user_id']);
            $table->foreign('history_chat_id')
                  ->references('id')->on('history_chats')
                  ->cascadeOnDelete();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_chat_pins');
    }
};
