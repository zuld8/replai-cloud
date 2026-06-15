<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('history_chats', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('status');
            }
            if (!Schema::hasColumn('history_chats', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('history_chats', function (Blueprint $table) {
            if (Schema::hasColumn('history_chats', 'is_archived')) {
                $table->dropColumn('is_archived');
            }
            if (Schema::hasColumn('history_chats', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
