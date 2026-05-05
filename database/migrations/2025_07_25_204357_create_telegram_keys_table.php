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
        Schema::create('telegram_keys', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('business_id')->index();
            $table->uuid('merchant_id')->index()->nullable();
            $table->string('name');
            $table->string('token');
            $table->text('agent')->nullable();
            $table->enum('auto_reply_method', ['all', 'chatbot', 'ai'])->default('chatbot');
            $table->uuid('fine_tunnel_id')->index()->nullable();
            $table->integer('limit_per_day')->default(75);
            $table->integer('daily_send')->default(0);
            $table->enum('status', ['active', 'no_active'])->default('no_active');
            $table->enum('daily_limit', ['yes', 'no'])->default('yes');
            $table->enum('auto_reply_certain_day', ['yes', 'no'])->default('no');
            $table->string('days')->nullable();
            $table->enum('auto_reply_certain_time', ['yes', 'no']);
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_keys');
    }
};
