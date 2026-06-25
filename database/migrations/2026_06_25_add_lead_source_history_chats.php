<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('history_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('history_chats', 'lead_source')) {
                $table->string('lead_source', 20)->nullable()->after('from');
            }
            if (!Schema::hasColumn('history_chats', 'lead_source_detail')) {
                $table->text('lead_source_detail')->nullable()->after('lead_source');
            }
        });
    }
    public function down(): void {
        Schema::table('history_chats', function (Blueprint $table) {
            $table->dropColumn(['lead_source', 'lead_source_detail']);
        });
    }
};
