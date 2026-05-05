<?php

namespace App\Jobs;

use App\Jobs\ProcessSingleFollowUpJob;
use App\Services\Sistem\QueueRouter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * CLEAN VERSION - Master Follow-up Job
 * Simple, reliable approach without complex queries
 */
class FollowUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    private const MAX_DISPATCHES_PER_RUN = 100;
    private const HIGH_PRIORITY_THRESHOLD = 30; // minutes

    /**
     * Execute the job - Find and dispatch eligible follow-ups
     */
    public function handle(): void
    {
        $startTime = microtime(true);

        try {
            // Get basic eligible chats
            $eligibleChats = $this->getEligibleChats();

            $dispatched = 0;
            $highPriorityCount = 0;

            foreach ($eligibleChats as $chat) {
                if ($dispatched >= self::MAX_DISPATCHES_PER_RUN) break;

                // Skip if recently processed
                if ($this->isRecentlyProcessed($chat->chat_id)) {
                    continue;
                }

                // Find next follow-up for this chat
                $nextFollowUp = $this->findNextFollowUp($chat);

                if (!$nextFollowUp) {
                    continue; // No follow-up available
                }

                // Check if it's time for this follow-up
                if ($chat->minutes_since_last_message < $nextFollowUp->delay) {
                    continue; // Not time yet
                }

                // Prepare chat data with follow-up info
                $chatData = $this->prepareChatData($chat, $nextFollowUp);

                // Determine priority
                $isHighPriority = $chat->minutes_since_last_message >= self::HIGH_PRIORITY_THRESHOLD;

                // Calculate delay (immediate for overdue, small random for others)
                $delay = $this->calculateDelay($chat, $nextFollowUp, $isHighPriority);

                $queue = QueueRouter::getQueue($chat->business_id, ($isHighPriority ? 'followup' : 'low'));
                dispatch(new ProcessSingleFollowUpJob($chatData))
                    ->onQueue($queue)
                    ->delay($delay);


                // Mark as processing
                $this->markAsProcessing($chat->chat_id);

                $dispatched++;
                if ($isHighPriority) $highPriorityCount++;
            }

            $executionTime = microtime(true) - $startTime;
        } catch (\Exception $e) {
            Log::error("FollowUpJob failed: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get basic eligible chats - SIMPLIFIED
     */
    private function getEligibleChats()
    {
        return DB::table('history_chats as hc')
            ->join('history_chat_details as hcd', 'hc.id', '=', 'hcd.history_chat_id')
            ->leftJoin('whatsapp_devices as d', function ($join) {
                $join->on('hc.device_id', '=', 'd.id')
                    ->where('hc.from', '=', 'whatsapp');
            })
            ->leftJoin('fine_tunnels as ft', 'd.fine_tunnel_id', '=', 'ft.id')
            ->where('hc.status', 'open')
            ->where('hc.takeover', 'no')
            ->where('hcd.from', 'device')
            ->where('hcd.is_follow_up', 'no')
            ->whereNotNull('ft.id')
            ->whereRaw('hcd.id = (
                SELECT MAX(hcd2.id) 
                FROM history_chat_details hcd2 
                WHERE hcd2.history_chat_id = hc.id 
                AND hcd2.from = "device" 
                AND hcd2.is_follow_up = "no"
            )')
            ->select([
                'hc.id as chat_id',
                'hc.from as chat_from',
                'hc.from_number',
                'hc.device_id',
                'hc.merchant_id',
                'hcd.id as last_message_id',
                'hcd.created_at as last_message_time',
                'ft.id as fine_tunnel_id',
                'ft.history_limit',
                'ft.model_ai',
                'hc.business_id',
                DB::raw('TIMESTAMPDIFF(MINUTE, hcd.created_at, NOW()) as minutes_since_last_message')
            ])
            ->orderBy('hcd.created_at')
            ->limit(self::MAX_DISPATCHES_PER_RUN * 2)
            ->get();
    }

    /**
     * Find next eligible follow-up for a chat - RESET-AWARE LOGIC
     */
    private function findNextFollowUp($chat)
    {
        // Step 1: Find the last "reset" message (AI reply without follow_up_id)
        $lastResetMessage = DB::table('history_chat_details')
            ->where('history_chat_id', $chat->chat_id)
            ->where('from', 'device')
            ->where('is_follow_up', 'no')
            ->whereNull('follow_up_id') // AI reply that resets sequence
            ->orderBy('id', 'desc')
            ->first();

        // Step 2: Get follow-ups sent AFTER the last reset
        $followUpsSinceReset = [];
        if ($lastResetMessage) {
            $followUpsSinceReset = DB::table('history_chat_details')
                ->where('history_chat_id', $chat->chat_id)
                ->where('from', 'device')
                ->where('id', '>', $lastResetMessage->id) // After reset message
                ->whereNotNull('follow_up_id')
                ->pluck('follow_up_id')
                ->unique()
                ->toArray();
        } else {
            // No reset found, get all follow-ups from beginning
            $followUpsSinceReset = DB::table('history_chat_details')
                ->where('history_chat_id', $chat->chat_id)
                ->where('from', 'device')
                ->whereNotNull('follow_up_id')
                ->pluck('follow_up_id')
                ->unique()
                ->toArray();
        }

        // Step 3: Find next available follow-up (by delay order) that hasn't been sent in current sequence
        $query = DB::table('follow_ups')
            ->where('finetunnel_id', $chat->fine_tunnel_id)
            ->orderBy('delay', 'asc')
            ->select(['id', 'delay', 'text', 'handoff', 'exact']);

        if (!empty($followUpsSinceReset)) {
            $query->whereNotIn('id', $followUpsSinceReset);
        }

        $nextFollowUp = $query->first();

        if ($nextFollowUp) {
        } else {
        }

        return $nextFollowUp;
    }

    /**
     * Prepare chat data with follow-up information
     */
    private function prepareChatData($chat, $followUp)
    {
        $chat->next_followup_id = $followUp->id;
        $chat->next_followup_delay = $followUp->delay;
        $chat->next_followup_text = $followUp->text;
        $chat->next_followup_handoff = $followUp->handoff;
        $chat->next_followup_exact = $followUp->exact;

        return $chat;
    }

    /**
     * Calculate optimal delay for job
     */
    private function calculateDelay($chat, $followUp, $isHighPriority): int
    {
        $minutesWaiting = $chat->minutes_since_last_message;
        $delayRequired = $followUp->delay;

        // If already overdue, send immediately (with small spread for high priority)
        if ($minutesWaiting >= $delayRequired) {
            return $isHighPriority ? 0 : rand(0, 30); // seconds
        }

        // Should not happen since we filter above, but just in case
        return 0;
    }

    /**
     * Check if chat is recently being processed
     */
    private function isRecentlyProcessed($chatId): bool
    {
        $cacheKey = "processing_followup_{$chatId}";
        return Cache::has($cacheKey);
    }

    /**
     * Mark chat as being processed
     */
    private function markAsProcessing($chatId): void
    {
        $cacheKey = "processing_followup_{$chatId}";
        Cache::put($cacheKey, true, now()->addMinutes(15)); // 15 minute lock
    }
}
