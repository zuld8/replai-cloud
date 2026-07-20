<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah unique constraint page_id di messanger_accounts.
     * Guard idempotent: cek duplikat & index sebelum eksekusi.
     * SEC P2-1: 1 Facebook Page = 1 bisnis (globally unique).
     */
    public function up(): void
    {
        // Guard: kalau masih ada duplikat, batalkan dengan pesan jelas
        $dupes = \DB::table('messenger_accounts')
            ->select('page_id')
            ->groupBy('page_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('page_id');

        if ($dupes->isNotEmpty()) {
            throw new \RuntimeException(
                'Masih ada page_id duplikat di messanger_accounts, bersihkan dulu: ' . $dupes->implode(', ')
            );
        }

        // Idempotent: skip kalau unique index sudah ada
        $exists = collect(\DB::select(
            "SHOW INDEX FROM messanger_accounts WHERE Key_name = 'messenger_accounts_page_id_unique'"
        ))->isNotEmpty();

        if (!$exists) {
            Schema::table('messenger_accounts', function (Blueprint $table) {
                // Drop index non-unique lama kalau ada (nama default Laravel: _page_id_index)
                $oldIndex = collect(\DB::select(
                    "SHOW INDEX FROM messanger_accounts WHERE Column_name = 'page_id' AND Non_unique = 1"
                ))->first();
                if ($oldIndex) {
                    $table->dropIndex([$oldIndex->Key_name ?? 'page_id']);
                }
                $table->unique('page_id', 'messenger_accounts_page_id_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('messenger_accounts', function (Blueprint $table) {
            $table->dropUnique('messenger_accounts_page_id_unique');
        });
    }
};
