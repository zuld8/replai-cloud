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
        Schema::table('instagram_accounts', function (Blueprint $table) { 
            $table->string('auto_reply_method')->default('chatbot')->after('details');
            $table->uuid('fine_tunnel_id')->index()->nullable()->after('auto_reply_method');
            $table->integer('limit_per_day')->default(75)->after('fine_tunnel_id');
            $table->integer('daily_send')->default(0)->after('limit_per_day');
            $table->enum('daily_limit', ['yes', 'no'])->default('yes');
            $table->enum('auto_reply_certain_day', ['yes', 'no'])->default('no');
            $table->string('days')->nullable();
            $table->enum('auto_reply_certain_time', ['yes', 'no']);
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
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
