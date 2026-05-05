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
        Schema::create('blash_details', function (Blueprint $table) {
            $table->uuid('id')->index()->unique();
            $table->uuid('store_id')->index();
            $table->uuid('blash_whatsapp_id')->index();
            $table->string('phone');
            $table->text('reports')->nullable();
            $table->enum('status', ['yes', 'no'])->default('yes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blash_details');
    }
};
