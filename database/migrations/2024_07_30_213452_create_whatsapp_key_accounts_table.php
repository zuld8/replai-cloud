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
        Schema::create('whatsapp_key_accounts', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->string('phone');
            $table->string('whatsapp_key')->nullable();
            $table->string('whatsapp_session')->nullable();
            $table->integer('limit_per_day')->default(75);
            $table->integer('daily_send')->default(0);
            $table->enum('status', ['active', 'no_active'])->default('no_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_key_accounts');
    }
};
