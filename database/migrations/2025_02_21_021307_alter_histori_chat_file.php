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
            $table->string('file_path')->nullable()->after('message');
            $table->string('file_type')->nullable()->after('file_path');
            $table->integer('file_size')->nullable()->after('file_type');
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
