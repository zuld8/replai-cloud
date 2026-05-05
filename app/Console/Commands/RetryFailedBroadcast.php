<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blash\BlashWhatsapp;
use App\Models\Blash\BlashDetail;
use App\Jobs\SendPromotionWhatsappBatchJob;
use App\Services\Sistem\QueueRouter;
use Illuminate\Support\Facades\Log;

class RetryFailedBroadcast extends Command
{
    protected $signature = 'broadcast:retry-failed 
                            {broadcast_id? : ID of specific broadcast to retry}
                            {--all : Retry all failed broadcasts from last 7 days}
                            {--error= : Only retry specific error code (e.g. 131042)}
                            {--dry-run : Show what would be retried without doing it}';
    
    protected $description = 'Retry ONLY failed recipients in a broadcast (no duplicates)';

    public function handle()
    {
        $broadcastId = $this->argument('broadcast_id');
        $retryAll = $this->option('all');
        $errorFilter = $this->option('error');
        $dryRun = $this->option('dry-run');

        if (!$broadcastId && !$retryAll) {
            $this->error('Please provide a broadcast_id or use --all flag');
            return 1;
        }

        if ($broadcastId) {
            $broadcasts = BlashWhatsapp::withoutGlobalScopes()
                ->where('id', $broadcastId)
                ->get();
        } else {
            $broadcasts = BlashWhatsapp::withoutGlobalScopes()
                ->where('created_at', '>=', now()->subDays(7))
                ->where('waba', 'yes')
                ->whereHas('details', function($q) {
                    $q->where('sending_status', 'no');
                })
                ->get();
        }

        if ($broadcasts->isEmpty()) {
            $this->info('No broadcasts found to retry.');
            return 0;
        }

        $totalRetried = 0;

        foreach ($broadcasts as $bc) {
            // Build query for failed details ONLY
            $query = BlashDetail::where('blash_whatsapp_id', $bc->id)
                ->where('sending_status', 'no');

            // Optionally filter by specific error code
            if ($errorFilter) {
                $query->where('reports', 'like', "%{$errorFilter}%");
            }

            $failedCount = $query->count();

            if ($failedCount == 0) {
                $this->line("  {$bc->name}: No failed recipients, skipping");
                continue;
            }

            $this->info("  {$bc->name}: {$failedCount} failed recipients");

            if ($dryRun) {
                // Show error breakdown
                $errors = BlashDetail::where('blash_whatsapp_id', $bc->id)
                    ->where('sending_status', 'no')
                    ->selectRaw('LEFT(reports, 80) as error, COUNT(*) as cnt')
                    ->groupBy('error')
                    ->orderByDesc('cnt')
                    ->get();
                foreach ($errors as $err) {
                    $this->line("    - {$err->error}: {$err->cnt}");
                }
                continue;
            }

            // RESET failed details for retry (clear reports, reset status)
            $failedIds = $query->pluck('id')->toArray();
            
            BlashDetail::whereIn('id', $failedIds)->update([
                'sending_status' => 'no',
                'status' => 'no',
                'reports' => null,
                'delivery_status' => 'queued',
                'delivery_error' => null,
                'wamid' => null,
            ]);

            // Update parent status back to pending
            $bc->update(['status' => 'pending']);

            // Dispatch in batches of 100
            $queue = QueueRouter::getQueue($bc->business_id, 'broadcast');
            $batches = 0;
            
            foreach (array_chunk($failedIds, 100) as $chunk) {
                SendPromotionWhatsappBatchJob::dispatch($chunk, 0, 0, 0)->onQueue($queue);
                $batches++;
            }

            $totalRetried += $failedCount;
            Log::info("RetryFailed: {$bc->name} — {$failedCount} failed recipients, {$batches} batches dispatched to {$queue}");
            $this->info("    → Dispatched {$batches} batches to queue [{$queue}]");
        }

        if ($dryRun) {
            $this->warn("\n  DRY RUN — nothing was actually retried. Remove --dry-run to execute.");
        } else {
            $this->info("\n  ✅ Total retried: {$totalRetried} recipients");
        }

        return 0;
    }
}
