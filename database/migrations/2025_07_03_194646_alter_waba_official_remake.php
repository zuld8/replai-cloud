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
            $table->uuid('meta_account_id')->nullable()->after('whatsapp_key');
        });

        Schema::table('message_templates', function (Blueprint $table) {
            $table->uuid('meta_account_id')->nullable()->after('name');
        }); 

        Schema::table('chat_bots', function (Blueprint $table) {
            $table->uuid('meta_account_id')->nullable()->after('keyword');
        }); 

        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->uuid('meta_account_id')->nullable()->after('waba_id');
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
