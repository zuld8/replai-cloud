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
            $table->enum('is_follow_up', ['yes', 'no'])->default('no')->after('messageid');
            $table->uuid('follow_up_id')->nullable()->after('is_follow_up');
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
