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
            $table->integer('scrapp_counter')->default(0)->after('merchant_id'); 
            $table->integer('whatsapp_sender')->default(0)->after('scrapp_counter'); 
            $table->integer('email_sender')->default(0)->after('whatsapp_sender'); 
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
