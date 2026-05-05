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
        Schema::table('couriers', function (Blueprint $table) {
            $table->string('service')->nullable()->after('status');
        });

        Schema::table('courier_fine_tunnels', function (Blueprint $table) {
            $table->string('service')->nullable()->after('code');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->enum('cek_ongkir', ['yes', 'no'])->after('limit_livechat')->default('no');
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->enum('cek_ongkir', ['yes', 'no'])->after('type')->default('no');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->enum('cek_ongkir_option_api', ['sistem', 'self'])->default('sistem')->after('is_online');
            $table->string('cek_ongkir_api')->nullable()->after('cek_ongkir_option_api');
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
