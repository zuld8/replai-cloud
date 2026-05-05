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
        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->longText('devices')->nullable();
            $table->enum('whatsapp_sender_notif', ['sequence', 'spin', 'random'])->default('sequence');
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
