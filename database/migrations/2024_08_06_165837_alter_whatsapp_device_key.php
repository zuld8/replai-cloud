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

        Schema::table('whatsapp_devices', function (Blueprint $table) {
            $table->text('whatsapp_key')->nullable()->after('phone');
            $table->uuid('whatsapp_session')->nullable()->after('whatsapp_key');
            $table->integer('limit_per_day')->default(75)->after('whatsapp_session');
            $table->integer('daily_send')->default(0)->after('limit_per_day');
            $table->enum('status', ['active', 'no_active'])->default('no_active')->after('daily_send');
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
