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
        Schema::create('history_chat_details', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('history_chat_id')->index();
            $table->enum('from', ['device', 'user'])->default('user');  
            $table->longText('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_chat_details');
    }
};
