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
        Schema::table('history_chats', function (Blueprint $table) {
            $table->string('name')->nullable()->after('from_number'); 
        });

        Schema::table('history_chat_details', function (Blueprint $table) {
            $table->uuid('reply_by_id')->nullable()->after('message');
            $table->string('type')->default('text')->after('reply_by_id');
            $table->string('media')->nullable()->after('type'); 
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
