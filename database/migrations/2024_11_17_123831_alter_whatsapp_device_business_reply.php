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
            $table->enum('reply_any_chat', ['yes', 'no'])->default('no')->after('auto_read_in_chattapp');
            $table->enum('reply_method', ['template', 'text'])->default('text')->after('reply_any_chat');
            $table->uuid('template_id')->nullable()->after('reply_method');
            $table->longText('reply_text')->nullable()->after('template_id');
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
