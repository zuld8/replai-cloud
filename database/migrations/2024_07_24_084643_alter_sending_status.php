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
        Schema::table('blash_details', function (Blueprint $table) {
            $table->enum('sending_status', ['yes', 'no'])->default('yes')->after('status');
        });

        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->enum('use', ['whatsapp', 'email'])->default('whatsapp')->after('status');
            $table->uuid('template_id')->index()->after('use');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->uuid('scrapping_id')->index()->nullable()->after('prospek');
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
