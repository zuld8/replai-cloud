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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('google_code')->nullable()->after('ongkir_method');
        });

        Schema::table('internal_settings', function (Blueprint $table) {
            $table->string('google_client_id')->nullable()->after('fb_config_id');
            $table->string('google_client_secret')->nullable()->after('google_client_id');
            $table->string('google_redirect')->nullable()->after('google_client_secret');
        });

        Schema::table('fine_tunnels', function (Blueprint $table) {
            $table->string('google_sheet_id')->nullable()->after('agent');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->enum('google_sheet', ['yes', 'no'])->default('no')->after('cek_ongkir');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->enum('google_sheet', ['yes', 'no'])->default('no')->after('cek_ongkir');
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
