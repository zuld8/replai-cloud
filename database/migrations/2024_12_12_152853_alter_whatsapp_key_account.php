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
        Schema::table('whatsapp_key_accounts', function (Blueprint $table) {
            $table->enum('auto_reply_method',['chatbot','ai'])->default('chatbot')->after('status');
            $table->uuid('fine_tunnel_id')->nullable()->after('auto_reply_method');
            $table->enum('daily_limit',['yes','no'])->default('no')->after('fine_tunnel_id');
            $table->enum('auto_reply_certain_day',['yes','no'])->default('no')->after('daily_limit');
            $table->string('days')->nullable()->after('auto_reply_certain_day');
            $table->enum('auto_reply_certain_time',['yes','no'])->default('no')->after('days');
            $table->string('start_time')->nullable()->after('auto_reply_certain_time');
            $table->string('end_time')->nullable()->after('start_time');
            $table->string('webhook')->nullable()->after('end_time');
            $table->enum('auto_read_before_autorespon',['yes','no'])->default('no')->after('webhook');
            $table->enum('reply_any_chat',['yes','no'])->default('no')->after('auto_read_before_autorespon');
            $table->enum('reply_method',['template','text'])->default('text');
            $table->uuid('template_id')->nullable()->after('reply_method');
            $table->longText('reply_text')->nullable()->after('template_id');
            $table->enum('auto_reply_option',['group','all','personal'])->default('all')->after('reply_text');
            $table->uuid('merchant_id')->index()->nullable()->after('auto_reply_option');
            $table->longText('meta_data')->nullable()->after('merchant_id');
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
