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
        Schema::table('internal_settings', function (Blueprint $table) {
            $table->string('fb_app_id')->nullable()->after('credit_token_advance');
            $table->string('fb_app_secret')->nullable()->after('fb_app_id');
            $table->string('fb_config_id')->nullable()->after('fb_app_secret');
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
