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

        // Settings to Business
        Schema::table('settings', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('logo')->nullable()->after('name');  
        });

        // Blash Whatsapp
        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Chatbot
        Schema::table('chat_bots', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Cities
        Schema::table('cities', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Ai Training
        Schema::table('fine_tunnels', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Folders
        Schema::table('folders', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // History Chats
        Schema::table('history_chats', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Logs
        Schema::table('logs', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Message Template
        Schema::table('message_templates', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Package Transactions
        Schema::table('package_transactions', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Provinces
        Schema::table('provinces', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Scraping
        Schema::table('scrappings', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Stores
        Schema::table('stores', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // User Access
        Schema::table('users', function (Blueprint $table) {
            $table->text('business_id')->after('merchant_id')->nullable();
        });

        // Whatsapp Devcie
        Schema::table('whatsapp_devices', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Whatsapp Key
        Schema::table('whatsapp_key_accounts', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
        });

        // Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('business_id')->after('merchant_id')->nullable();
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
