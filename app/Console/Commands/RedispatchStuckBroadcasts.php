<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blash\BlashWhatsapp;
use App\Models\Blash\BlashDetail;
use App\Jobs\SendPromotionWhatsappBatchJob;
use App\Services\Sistem\QueueRouter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RedispatchStuckBroadcasts extends Command
{
    protected $signature = 'broadcast:redispatch-stuck';
    protected $description = 'Re-dispatch broadcasts with unsent details and mark complete when done';

    public function handle()
    {
        // Prevent concurrent execution with a lock
        $lockKey = 'broadcast:redispatch-stuck:lock';
        if (!Cache::lock($lockKey, 300)->get()) {
            Log::info('RedispatchStuck: Another instance is running, skipping');
            return;
        }

        try {
            $this->doHandle();
        } finally {
            Cache::lock($lockKey)->forceRelease();
        }
    }

    private function doHandle()
    {
        // 1. Find broadcasts with unsent details (processing or success status)
        // Only look at broadcasts created > 10 minutes ago (give initial dispatch time to work)
        $stuckIds = \DB::select("
            SELECT DISTINCT bw.id
            FROM blash_whatsapps bw
            INNER JOIN blash_details bd ON bd.blash_whatsapp_id = bw.id
                AND bd.sending_status != 'yes'
            WHERE (bw.status IN ('processing', 'success', 'pending') OR bw.status = '' OR bw.status IS NULL)
            AND bw.waba = 'yes'
            AND bw.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND bw.created_at <= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ");

        if (empty($stuckIds)) {
            $this->markCompleted();
            return;
        }

        Log::info('RedispatchStuck: Found ' . count($stuckIds) . ' broadcasts with unsent');

        foreach ($stuckIds as $row) {
            $bc = BlashWhatsapp::withoutGlobalScopes()->find($row->id);
            if (!$bc) continue;

            // Per-broadcast lock to prevent double dispatch
            $bcLock = "broadcast:dispatching:{$bc->id}";
            if (!Cache::lock($bcLock, 600)->get()) {
                Log::info("RedispatchStuck: {$bc->name} — already being dispatched, skipping");
                continue;
            }

            try {
                $unsent = BlashDetail::where('blash_whatsapp_id', $bc->id)
                    ->where('sending_status', '!=', 'yes')
                    ->count();

                if ($unsent == 0) {
                    $bc->update(['status' => 'success']);
                    Log::info("RedispatchStuck: {$bc->name} — ALL SENT, marked success");
                    continue;
                }

                // DEDUP CHECK: Remove duplicate phone entries BEFORE dispatching
                $this->removeDuplicatePhones($bc);

                // Only redispatch entries that are genuinely stuck:
                // - No reports (never processed) 
                // - Retryable errors
                // - AND not recently dispatched (check delivery_status != 'dispatched')
                $retryableErrors = ['Device WABA tidak tersedia', 'Tidak dapat mendeteksi', 'timeout'];
                
                $query = BlashDetail::where('blash_whatsapp_id', $bc->id)
                    ->where('sending_status', '!=', 'yes')
                    ->where(function($q) use ($retryableErrors) {
                        $q->whereNull('reports');
                        foreach ($retryableErrors as $err) {
                            $q->orWhere(function($sq) use ($err) {
                                $sq->where('reports', 'like', "%{$err}%");
                            });
                        }
                    })
                    // CRITICAL: Skip entries that were dispatched recently (< 15 min ago)
                    ->where(function($q) {
                        $q->whereNull('delivery_status')
                          ->orWhere('delivery_status', 'queued')
                          ->orWhere('delivery_status', '');
                    });

                $ids = $query->pluck('id')->toArray();

                if (empty($ids)) {
                    // Check if all unsent have permanent non-retryable errors (131049/131026)
                    $permFailed = \App\Models\Blash\BlashDetail::where('blash_whatsapp_id', $bc->id)
                        ->where('sending_status', '!=', 'yes')
                        ->where('delivery_status', 'failed')
                        ->count();
                    if ($permFailed > 0) {
                        $bc->update(['status' => 'partial_success']);
                        Log::warning("RedispatchStuck: {$bc->name} - {$permFailed} perm failed, marked partial_success");
                    } else {
                        Log::info("RedispatchStuck: {$bc->name} - no retryable entries found");
                    }
                    continue;
                }

                // Mark as 'dispatched' BEFORE dispatching to prevent re-dispatch
                BlashDetail::whereIn('id', $ids)->update([
                    'reports' => null, 
                    'delivery_error' => null, 
                    'delivery_status' => 'dispatched'
                ]);

                $queue = QueueRouter::getQueue($bc->business_id, 'broadcast');
                $batches = 0;

                foreach (array_chunk($ids, 100) as $chunk) {
                    SendPromotionWhatsappBatchJob::dispatch($chunk, 0, 0, 0)->onQueue($queue);
                    $batches++;
                }

                if ($bc->status !== 'processing') {
                    $bc->update(['status' => 'processing']);
                }

                Log::info("RedispatchStuck: {$bc->name} — {$unsent} unsent, dispatching {$batches} batches ({$batches} x 100 IDs) to {$queue}");
            } finally {
                Cache::lock($bcLock)->forceRelease();
            }
        }

        $this->markCompleted();
    }

    /**
     * Remove duplicate phone entries from a broadcast
     * Keeps the first (earliest) entry per phone
     */
    private function removeDuplicatePhones(BlashWhatsapp $bc)
    {
        $dupes = \DB::select("
            SELECT phone, MIN(id) as keep_id, COUNT(*) as cnt
            FROM blash_details
            WHERE blash_whatsapp_id = ?
            GROUP BY phone
            HAVING cnt > 1
        ", [$bc->id]);

        if (empty($dupes)) return;

        $totalRemoved = 0;
        foreach ($dupes as $d) {
            $deleted = BlashDetail::where('blash_whatsapp_id', $bc->id)
                ->where('phone', $d->phone)
                ->where('id', '!=', $d->keep_id)
                ->delete();
            $totalRemoved += $deleted;
        }

        if ($totalRemoved > 0) {
            Log::warning("RedispatchStuck: {$bc->name} — REMOVED {$totalRemoved} duplicate phone entries");
        }
    }

    private function markCompleted()
    {
        $processing = BlashWhatsapp::whereIn('status', ['processing', ''])->orWhereNull('status')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        foreach ($processing as $bc) {
            $unsent = BlashDetail::where('blash_whatsapp_id', $bc->id)
                ->where('sending_status', '!=', 'yes')
                ->count();

            if ($unsent == 0) {
                $bc->update(['status' => 'success']);
                Log::info("RedispatchStuck: {$bc->name} — completed, marked success");
            }
        }
    }
}
