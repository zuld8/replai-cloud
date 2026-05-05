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
        Schema::create('history_chat_information', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('history_chat_id')->index();
            $table->enum('type', ['text', 'number', 'select', 'date', 'true_false'])->default('text');
            $table->string('label');
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_chat_information');
    }
};
