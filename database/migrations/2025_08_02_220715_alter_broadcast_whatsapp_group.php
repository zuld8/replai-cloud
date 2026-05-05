<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->string('use')->default('whatsapp')->change();
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->string('type')->default('whatsapp')->nullable()->change();
        });

        // DB::statement("ALTER TABLE blash_whatsapps MODIFY COLUMN `use` ENUM('whatsapp', 'email', 'whatsapp_follow_up','whatsapp_group') default 'whatsapp'");
        // DB::statement("ALTER TABLE logs MODIFY COLUMN `type` ENUM('whatsapp', 'email', 'scrapping','whatsapp_group') default 'whatsapp'");

        Schema::table('blash_details', function (Blueprint $table) {
            $table->uuid('store_id')->nullable()->change();
            $table->uuid('whatsapp_group_id')->nullable()->after('store_id');
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
