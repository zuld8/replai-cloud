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
            $table->enum('daily_limit', ['yes', 'no'])->default('yes')->after('fine_tunnel_id');
            $table->enum('auto_reply_certain_day', ['yes', 'no'])->default('no')->after('daily_limit');
            $table->string('days')->nullable()->after('auto_reply_certain_day');
            $table->enum('auto_reply_certain_time', ['yes', 'no'])->default('no')->after('days');
            $table->string('start_time')->nullable()->after('auto_reply_certain_time');
            $table->string('end_time')->nullable()->after('start_time');
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
