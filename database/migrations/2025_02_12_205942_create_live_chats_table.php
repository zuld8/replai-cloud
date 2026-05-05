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
        Schema::create('live_chats', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('agent')->nullable();
            $table->enum('type',['chatbot','ai','all'])->default('all'); 
            $table->uuid('finetunnel_id')->index()->nullable();
            $table->uuid('merchant_id')->index();
            $table->uuid('business_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_chats');
    }
};
