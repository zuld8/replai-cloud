<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fine_tunnels', function (Blueprint $table) {
            if (!Schema::hasColumn('fine_tunnels', 'knowledge_manual')) {
                $table->text('knowledge_manual')->nullable()->after('description');
            }
        });
    }
    public function down(): void
    {
        Schema::table('fine_tunnels', function (Blueprint $table) {
            if (Schema::hasColumn('fine_tunnels', 'knowledge_manual')) {
                $table->dropColumn('knowledge_manual');
            }
        });
    }
};
