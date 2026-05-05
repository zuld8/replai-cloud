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
            $table->integer('stop_sending')->default(20)->after('delay_message');
            $table->integer('rest_sending')->default(90)->after('stop_sending');
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
