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
            $table->string('instagram_config_id')->nullable()->after('fb_config_id');
            $table->string('messanger_config_id')->nullable()->after('instagram_config_id');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->enum('limit_instagram', ['yes', 'no'])->default('yes')->after('cek_ongkir');
            $table->integer('instagram')->default(0)->after('limit_instagram');

            $table->enum('limit_messanger', ['yes', 'no'])->default('yes')->after('instagram');
            $table->integer('messanger')->default(0)->after('limit_messanger');

            $table->enum('limit_telegram', ['yes', 'no'])->default('yes')->after('messanger');
            $table->integer('telegram')->default(0)->after('limit_telegram');
        });

         Schema::table('package_transactions', function (Blueprint $table) {
            $table->enum('limit_instagram', ['yes', 'no'])->default('yes')->after('cek_ongkir');
            $table->integer('instagram')->default(0)->after('limit_instagram');

            $table->enum('limit_messanger', ['yes', 'no'])->default('yes')->after('instagram');
            $table->integer('messanger')->default(0)->after('limit_messanger');

            $table->enum('limit_telegram', ['yes', 'no'])->default('yes')->after('messanger');
            $table->integer('telegram')->default(0)->after('limit_telegram');
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
