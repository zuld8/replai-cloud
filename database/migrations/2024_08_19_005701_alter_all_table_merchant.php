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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('gender');
            $table->string('phone')->index()->unique()->after('role');
            $table->uuid('merchant_id')->index()->nullable()->after('phone');
            $table->softDeletes();
        });

        Schema::table('blash_whatsapps', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('template_id'); 
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('name'); 
        }); 

        Schema::table('chat_bots', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('message'); 
        }); 

        Schema::table('cities', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('status'); 
        }); 

        Schema::table('fine_tunnels', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('status'); 
        }); 

        Schema::table('logs', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('status'); 
        }); 

        Schema::table('message_templates', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('message'); 
        }); 

        Schema::table('provinces', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('status'); 
        }); 

        Schema::table('scrappings', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('status'); 
        }); 

        Schema::table('settings', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('local_api_key'); 
        }); 

        Schema::table('stores', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('scrapping_id'); 
        });  

        Schema::table('whatsapp_devices', function (Blueprint $table) {
            $table->uuid('merchant_id')->nullable()->after('end_time'); 
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
