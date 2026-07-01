<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_chat_details', function (Blueprint $table) {
            if (!Schema::hasColumn('history_chat_details', 'extra')) {
                $table->text('extra')->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('history_chat_details', function (Blueprint $table) {
            if (Schema::hasColumn('history_chat_details', 'extra')) {
                $table->dropColumn('extra');
            }
        });
    }
};
