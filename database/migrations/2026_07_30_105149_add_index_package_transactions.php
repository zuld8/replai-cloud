<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!$this->indexExists('package_transactions', 'idx_pt_biz_type_status')) {
            DB::statement('ALTER TABLE `package_transactions`
                ADD INDEX `idx_pt_biz_type_status` (`business_id`, `type`, `status`, `expire_date`),
                ALGORITHM=INPLACE, LOCK=NONE');
        }
    }

    public function down(): void
    {
        if ($this->indexExists('package_transactions', 'idx_pt_biz_type_status')) {
            DB::statement('ALTER TABLE `package_transactions` DROP INDEX `idx_pt_biz_type_status`');
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $result = DB::select(
            "SELECT COUNT(*) as cnt FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
            [$table, $index]
        );
        return $result[0]->cnt > 0;
    }
};
