<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillAiTokens extends Command
{
    protected $signature   = 'ai:backfill-tokens';
    protected $description = 'Backfill estimasi total_tokens dari credit_using untuk data lama';

    public function handle(): int
    {
        // Ambil token_per_credit dari InternalSetting
        $setting = DB::table('internal_settings')->first(['credit_token_basic']);
        $tpc     = (int) ($setting->credit_token_basic ?? 250);
        $this->info("token_per_credit = {$tpc}");

        // Hitung total rows yang butuh backfill
        $total = DB::table('history_chat_details')
            ->where('credit_using', '>', 0)
            ->where(function ($q) {
                $q->whereNull('total_tokens')->orWhere('total_tokens', 0);
            })->count();

        $this->info("Rows yang butuh backfill: {$total}");

        if ($total === 0) {
            $this->info("Tidak ada yang perlu di-backfill.");
            return self::SUCCESS;
        }

        // Backfill ber-batch via raw SQL + LIMIT untuk menghindari lock lama
        $batchSize = 2000;
        $updated   = 0;
        $bar       = $this->output->createProgressBar(ceil($total / $batchSize));
        $bar->start();

        do {
            // Update batch: ambil min id dulu untuk pakai index-seek
            $affected = DB::statement("
                UPDATE history_chat_details
                SET total_tokens = ROUND(credit_using * ?)
                WHERE credit_using > 0
                  AND (total_tokens IS NULL OR total_tokens = 0)
                LIMIT ?
            ", [$tpc, $batchSize]);

            // Cek berapa yang ke-update di batch ini
            $batchAffected = DB::select('SELECT ROW_COUNT() as n')[0]->n ?? 0;
            $updated += $batchAffected;
            $bar->advance();

            // Jeda kecil agar tidak terus-menerus lock (friendly ke prod)
            usleep(50000); // 50ms

        } while ($batchAffected >= $batchSize);

        $bar->finish();
        $this->newLine();
        $this->info("✅ Backfill selesai — {$updated} rows diupdate (estimasi dari credit_using × {$tpc}).");
        $this->warn("Catatan: ini ESTIMASI token untuk data sebelum deploy 2026-08-12. Data baru = token asli.");

        return self::SUCCESS;
    }
}
