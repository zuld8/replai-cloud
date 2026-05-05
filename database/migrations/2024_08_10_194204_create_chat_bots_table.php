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
        Schema::create('chat_bots', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->string('keyword');
            $table->text('select_device');
            $table->enum('reply_method',['text','template'])->default('text');
            $table->uuid('template_id')->index()->nullable();
            $table->longText('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_bots');
    }
};
