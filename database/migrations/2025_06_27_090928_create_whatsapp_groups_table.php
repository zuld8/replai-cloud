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
        Schema::create('whatsapp_groups', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('scrapping_id')->index()->nullable();
            $table->uuid('business_id')->index()->nullable();
            $table->uuid('merchant_id')->index()->nullable(); 
            $table->uuid('device_id')->index()->nullable();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('group_id')->nullable();
            $table->enum('scraping', ['yes', 'no'])->default('no')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_groups');
    }
};
