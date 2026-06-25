<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_chat_details', function (Blueprint $table) {
            if (!Schema::hasColumn('history_chat_details', 'source')) {
                $table->string('source', 20)->nullable()->after('from')
                    ->comment('broadcast|bot|notification|agent|system');
            }
            if (!Schema::hasColumn('history_chat_details', 'buttons')) {
                $table->text('buttons')->nullable()->after('source')
                    ->comment('JSON array of template buttons');
            }
        });
    }

    public function down(): void
    {
        Schema::table('history_chat_details', function (Blueprint $table) {
            $table->dropColumn(['source', 'buttons']);
        });
    }
};
