<?php

namespace App\Console\Commands\Broadcast;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Recompute stat_* columns from blash_details (source of truth per-recipient).
 * Fixes overlapping/incorrect cached stats on old broadcasts.
 *
 * Usage: php artisan broadcast:recompute-stats [--id=UUID] [--dry-run]
 */
class RecomputeBroadcastStats extends Command
{
    protected $signature = 'broadcast:recompute-stats
                            {--id= : Recompute single broadcast by ID}
                            {--dry-run : Show changes without saving}
                            {--limit=500 : Max broadcasts to process}';

    protected $description = 'Recompute stat_delivered/stat_read/stat_failed/stat_total from blash_details (mutually-exclusive funnel)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $singleId = $this->option('id');
        $limit    = (int) $this->option('limit');

        $this->info($dryRun ? '[DRY RUN] ' : '' . 'Recomputing broadcast stats from blash_details...');

        $query = DB::table('blash_whatsapps')->where('waba', 'yes');
        if ($singleId) {
            $query->where('id', $singleId);
        } else {
            $query->limit($limit);
        }

        $broadcasts = $query->select('id', 'stat_total', 'stat_delivered', 'stat_read', 'stat_failed', 'stat_delivery_failed')->get();

        $this->info("Processing {$broadcasts->count()} broadcasts...");

        $changed = 0;
        $bar = $this->output->createProgressBar($broadcasts->count());

        foreach ($broadcasts as $bc) {
            // Aggregate delivery_status from blash_details
            // Funnel: read > delivered > sent — take highest state per recipient
            $stats = DB::select("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN delivery_status = 'read'                              THEN 1 ELSE 0 END) as stat_read,
                    SUM(CASE WHEN delivery_status = 'delivered'                          THEN 1 ELSE 0 END) as stat_delivered,
                    SUM(CASE WHEN delivery_status IN ('sent','dispatched','processing')  THEN 1 ELSE 0 END) as stat_sent,
                    SUM(CASE WHEN delivery_status = 'failed'                             THEN 1 ELSE 0 END) as stat_failed
                FROM blash_details
                WHERE blash_whatsapp_id = ?
            ", [$bc->id]);

            if (empty($stats)) {
                $bar->advance();
                continue;
            }

            $s = $stats[0];
            $newTotal     = (int) $s->total;
            $newRead      = (int) $s->stat_read;
            $newDelivered = (int) $s->stat_delivered;
            $newFailed    = (int) $s->stat_failed;
            // pending = total - (read + delivered + failed) — no separate column needed

            $hasChange = (
                $bc->stat_total     !== $newTotal     ||
                $bc->stat_read      !== $newRead      ||
                $bc->stat_delivered !== $newDelivered ||
                $bc->stat_failed    !== $newFailed
            );

            if ($hasChange) {
                $changed++;
                if (!$dryRun) {
                    DB::table('blash_whatsapps')->where('id', $bc->id)->update([
                        'stat_total'     => $newTotal,
                        'stat_read'      => $newRead,
                        'stat_delivered' => $newDelivered,
                        'stat_failed'    => $newFailed,
                        'stat_updated_at'=> now(),
                    ]);
                } else {
                    $this->line("\n  [CHANGE] {$bc->id}: total {$bc->stat_total}→{$newTotal} | delivered {$bc->stat_delivered}→{$newDelivered} | read {$bc->stat_read}→{$newRead} | failed {$bc->stat_failed}→{$newFailed}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. {$changed} broadcasts " . ($dryRun ? 'would be ' : '') . "updated.");

        return Command::SUCCESS;
    }
}
