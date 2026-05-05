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
        Schema::table('message_templates', function (Blueprint $table) {
            $table->enum('type', ['whatsapp', 'email'])->default('whatsapp')->after('merchant_id');
            $table->enum('type_content', ['button', 'list', 'location', 'description'])->default('description')->after('type');
            $table->longText('button_or_list')->nullable()->after('type_content');
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
