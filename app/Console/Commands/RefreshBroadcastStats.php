<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshBroadcastStats extends Command
{
    protected $signature   = 'broadcast:refresh-stats {--chunk=50 : Rows per batch} {--id= : Specific broadcast ID}';
    protected $description = 'Recalculate and cache stat columns in blash_whatsapps';

    public function handle(): int
    {
        $chunkSize = (int) $this->option('chunk');
        $specificId = $this->option('id');

        $query = DB::table('blash_whatsapps');
        if ($specificId) {
            $query->where('id', $specificId);
        }

        $total = $query->count();
        $this->info("Refreshing stats for {$total} broadcasts...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->orderBy('created_at', 'desc')->chunk($chunkSize, function ($rows) use ($bar) {
            foreach ($rows as $bw) {
                $stats = DB::table('blash_details')
                    ->where('blash_whatsapp_id', $bw->id)
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(sending_status = "yes") as sent,
                        SUM(sending_status = "no") as failed,
                        SUM(delivery_status = "delivered") as delivered,
                        SUM(delivery_status = "read") as stat_read,
                        SUM(delivery_status = "failed") as delivery_failed
                    ')
                    ->first();

                DB::table('blash_whatsapps')->where('id', $bw->id)->update([
                    'stat_total'            => (int) ($stats->total ?? 0),
                    'stat_sent'             => (int) ($stats->sent ?? 0),
                    'stat_failed'           => (int) ($stats->failed ?? 0),
                    'stat_delivered'        => (int) ($stats->delivered ?? 0),
                    'stat_read'             => (int) ($stats->stat_read ?? 0),
                    'stat_delivery_failed'  => (int) ($stats->delivery_failed ?? 0),
                    'stat_updated_at'       => now(),
                ]);

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Done! Stats refreshed for {$total} broadcasts.");

        return Command::SUCCESS;
    }
}
