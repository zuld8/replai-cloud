<?php

namespace App\Console\Commands;

use App\Models\Blash\BlashWhatsapp;
use App\Jobs\SendWhatsappJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Rescue broadcasts that are pending + overdue (>2min) but have 0 details.
 * This handles edge cases where the main SendWhatsappJob cron missed the window.
 * Runs every 2 minutes via scheduler.
 */
class RescueStuckPendingBroadcasts extends Command
{
    protected $signature = 'broadcast:rescue-pending';
    protected $description = 'Rescue pending broadcasts that are overdue but have 0 details (never started)';

    public function handle()
    {
        // Find broadcasts that are:
        // 1. status = pending
        // 2. schedule <= now - 2 minutes (overdue by at least 2 min)
        // 3. use = whatsapp
        // 4. have 0 blash_details (never started)
        $overdue = BlashWhatsapp::withoutGlobalScopes()
            ->where('status', 'pending')
            ->where('use', 'whatsapp')
            ->where('schedule', '<=', now()->subMinutes(2))
            ->whereDoesntHave('details')
            ->orderBy('schedule', 'asc')
            ->get();

        if ($overdue->isEmpty()) {
            return;
        }

        foreach ($overdue as $bc) {
            $lockKey = "broadcast:creating:{$bc->id}";

            // Skip if already being processed
            if (Cache::has($lockKey)) {
                Log::info("RescuePending: {$bc->name} — lock held, skipping");
                continue;
            }

            Log::warning("RescuePending: {$bc->name} ({$bc->id}) — overdue {$bc->schedule}, dispatching rescue");
        }

        if ($overdue->count() > 0) {
            // Dispatch SendWhatsappJob to process all pending broadcasts
            // Clear any stale overlap locks first
            try {
                $redis = \Illuminate\Support\Facades\Redis::connection();
                $keys = $redis->keys('*broadcast:creating*');
                foreach ($keys as $key) {
                    $ttl = $redis->ttl($key);
                    if ($ttl === -1 || $ttl > 55) {
                        $redis->del($key);
                        Log::info("RescuePending: Cleared stale lock {$key}");
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }

            SendWhatsappJob::dispatch();
            Log::warning("RescuePending: Dispatched SendWhatsappJob for {$overdue->count()} overdue broadcast(s)");
            $this->info("Rescued {$overdue->count()} overdue broadcast(s)");
        }
    }
}
