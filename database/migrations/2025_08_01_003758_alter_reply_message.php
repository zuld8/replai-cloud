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
        Schema::table('history_chat_details', function (Blueprint $table) {
            $table->string('reply_to')->nullable()->after('message');
            $table->text('reply_text')->nullable()->after('reply_to');
            $table->json('quoted_message')->nullable()->after('reply_text');
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
