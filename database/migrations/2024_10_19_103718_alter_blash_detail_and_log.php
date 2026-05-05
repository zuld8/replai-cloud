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
            $table->timestamp('sending')->nullable()->after('type');
            $table->longText('text')->nullable()->after('sending');
            $table->uuid('device_id')->nullable()->after('text'); 
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->uuid('store_id')->nullable()->after('merchant_id');
            $table->timestamp('sending')->nullable()->after('store_id');
            $table->longText('text')->nullable()->after('sending');
            $table->uuid('device_id')->nullable()->after('text'); 
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
