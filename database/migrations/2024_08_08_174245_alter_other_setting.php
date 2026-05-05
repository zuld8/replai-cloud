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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('timezone')->default('Asia/Jakarta')->after('use_whatsapp');
            $table->enum('scrapp_phone',['protect_double','no_protect'])->default('protect_double')->after('timezone');
            $table->enum('scrapp_phone_whatsapp',['must_whatsapp','all'])->default('must_whatsapp')->after('scrapp_phone');
            $table->string('phone_country_code')->default('62')->after('scrapp_phone_whatsapp'); 
            $table->string('default_lang')->default('id')->after('phone_country_code');
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
