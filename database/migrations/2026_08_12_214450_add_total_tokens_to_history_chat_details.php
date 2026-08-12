<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('history_chat_details', function (Blueprint $table) {
            // Simpan raw token per balasan AI — untuk dashboard admin token tracking
            // created_at index sudah ada (idx_hcd_created_source) → tidak perlu tambah
            $table->unsignedBigInteger('total_tokens')
                  ->nullable()->default(0)->after('credit_using');
        });
    }

    public function down(): void
    {
        Schema::table('history_chat_details', function (Blueprint $table) {
            $table->dropColumn('total_tokens');
        });
    }
};
