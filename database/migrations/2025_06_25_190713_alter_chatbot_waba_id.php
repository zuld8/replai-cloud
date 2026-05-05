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
        Schema::table('chat_bots', function (Blueprint $table) {
            $table->uuid('whatsapp_waba_id')->nullable()->after('business_id');
            $table->text('select_instagram')->nullable()->after('whatsapp_waba_id');
            $table->text('select_messanger')->nullable()->after('select_instagram');
            $table->longText('metadata')->nullable()->after('select_messanger');
            $table->string('file')->nullable()->after('metadata');
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
