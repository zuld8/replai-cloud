<?php

namespace App\Jobs;

use App\Http\Resources\LiveChat\HistoryChatResources;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\Setting;
use App\Observers\ChatBot\GeminiAiServiceObserver;
use App\Observers\ChatBot\OpenAiServiceObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessSingleFollowUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 30;
    public $tries = 2;
    public $chatData;

    public function __construct($chatData)
    {
        $this->chatData = $chatData;
    }

    /**
     * Process single follow-up - IMPROVED with proper timing validation
     */
    public function handle(): void
    {
        // Clear processing lock
        $this->clearProcessingLock();

        // Re-validate eligibility and timing
        if (!$this->isStillEligibleWithTiming()) { 
            return;
        }

        try {
            $this->processFollowUp();
        } catch (\Exception $e) {
            Log::error("ProcessSingleFollowUpJob failed for chat {$this->chatData->chat_id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enhanced eligibility check - SIMPLIFIED
     */
    private function isStillEligibleWithTiming(): bool
    {
        // Check basic chat status
        $chat = DB::table('history_chats')
            ->where('id', $this->chatData->chat_id)
            ->where('status', 'open')
            ->where('takeover', 'no')
            ->first();

        if (!$chat) { 
            return false;
        }

        // Get current minutes since last message
        $currentMinutesSince = DB::table('history_chat_details')
            ->where('id', $this->chatData->last_message_id)
            ->select(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes_elapsed'))
            ->first();

        if (!$currentMinutesSince) { 
            return false;
        }

        // Check if we have follow-up info (should be passed from dispatcher)
        if (!isset($this->chatData->next_followup_id) || !isset($this->chatData->next_followup_delay)) { 
            return false;
        }

        // Check timing - must have waited at least the required delay
        $requiredDelay = $this->chatData->next_followup_delay;
        $actualMinutesWaited = $currentMinutesSince->minutes_elapsed;

        if ($actualMinutesWaited < $requiredDelay) { 
            return false;
        }

        // Check if this specific follow-up hasn't been sent yet
        $alreadySent = DB::table('history_chat_details')
            ->where('history_chat_id', $this->chatData->chat_id)
            ->where('follow_up_id', $this->chatData->next_followup_id)
            ->exists();

        if ($alreadySent) {
         
            return false;
        }
 

        return true;
    }

    /**
     * Clear processing lock when job starts
     */
    private function clearProcessingLock(): void
    {
        $cacheKey = "processing_followup_{$this->chatData->chat_id}";
        Cache::forget($cacheKey);
    }

    /**
     * Process follow-up with enhanced validation and debugging
     */
    private function processFollowUp(): void
    {
        try {
            DB::beginTransaction();

            // Debug: Log current state before processing
          

            // Step 1: Check credit eligibility
            if (!$this->canSendFollowUp()) {
                // DON'T mark as followed up if it's just credit issue
                DB::commit(); 

                // Reschedule for later (30 minutes)
                self::dispatch($this->chatData)
                    ->delay(now()->addMinutes(30))
                    ->onQueue('followup-low-priority');
                return;
            }

            // Step 2: Use the follow-up data from dispatcher
            $followUpData = (object)[
                'id' => $this->chatData->next_followup_id,
                'text' => $this->chatData->next_followup_text,
                'handoff' => $this->chatData->next_followup_handoff,
                'exact' => $this->chatData->next_followup_exact,
                'finetunnel_id' => $this->chatData->fine_tunnel_id
            ];

            // Step 3: Generate response
            $response = $this->generateFollowUpResponse($followUpData);

            if (!$response) { 
                throw new \Exception("Failed to generate follow-up response");
            }

            // Step 4: Handle handoff if needed
            if ($followUpData->handoff === 'yes') {
                DB::table('history_chats')
                    ->where('id', $this->chatData->chat_id)
                    ->update(['takeover' => 'yes']);
 
            }

            // Step 5: Create follow-up message record
            $followUpMessage = $this->createFollowUpMessage($response, $followUpData);

            // Step 6: Mark original message as followed up ONLY if this is the last follow-up
            $isLastFollowUp = $this->isLastFollowUp($followUpData);
            if ($isLastFollowUp) {
                $this->markMessageAsFollowedUp(); 
            } else { 
            }

            // Step 7: Clear related cache
            $this->clearFollowUpCache();

            DB::commit();

            // Step 8: Send external notifications
            $this->sendExternalNotifications($response, $followUpMessage);

            // Final log with complete status
            $finalStatus = $this->getFollowUpSequenceStatus(); 
        } catch (\Exception $e) {
            DB::rollback(); 
            throw $e;
        }
    }

    /**
     * Get current minutes since last message for logging
     */
    private function getCurrentMinutesSinceLastMessage(): int
    {
        $result = DB::table('history_chat_details')
            ->where('id', $this->chatData->last_message_id)
            ->select(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes'))
            ->first();

        return $result ? $result->minutes : 0;
    }

    /**
     * Get follow-up sequence status for debugging - RESET-AWARE
     */
    private function getFollowUpSequenceStatus(): array
    {
        // Get all follow-ups for this fine tunnel
        $allFollowUps = DB::table('follow_ups')
            ->where('finetunnel_id', $this->chatData->fine_tunnel_id)
            ->orderBy('delay', 'asc')
            ->select(['id', 'delay', 'text'])
            ->get();

        // Find the last reset message
        $lastResetMessage = DB::table('history_chat_details')
            ->where('history_chat_id', $this->chatData->chat_id)
            ->where('from', 'device')
            ->where('is_follow_up', 'no')
            ->whereNull('follow_up_id')
            ->orderBy('id', 'desc')
            ->select(['id', 'created_at', 'message'])
            ->first();

        // Get follow-up messages since reset
        $followUpMessagesSinceReset = collect();
        if ($lastResetMessage) {
            $followUpMessagesSinceReset = DB::table('history_chat_details as hcd')
                ->join('follow_ups as fu', 'hcd.follow_up_id', '=', 'fu.id')
                ->where('hcd.history_chat_id', $this->chatData->chat_id)
                ->where('hcd.id', '>', $lastResetMessage->id)
                ->whereNotNull('hcd.follow_up_id')
                ->select([
                    'fu.id as follow_up_id',
                    'fu.delay',
                    'hcd.created_at as sent_at',
                    'hcd.is_follow_up',
                    'hcd.message'
                ])
                ->orderBy('hcd.created_at', 'asc')
                ->get();
        } else {
            // No reset found, get all follow-up messages
            $followUpMessagesSinceReset = DB::table('history_chat_details as hcd')
                ->join('follow_ups as fu', 'hcd.follow_up_id', '=', 'fu.id')
                ->where('hcd.history_chat_id', $this->chatData->chat_id)
                ->whereNotNull('hcd.follow_up_id')
                ->select([
                    'fu.id as follow_up_id',
                    'fu.delay',
                    'hcd.created_at as sent_at',
                    'hcd.is_follow_up',
                    'hcd.message'
                ])
                ->orderBy('hcd.created_at', 'asc')
                ->get();
        }

        $sentFollowUpIds = $followUpMessagesSinceReset->pluck('follow_up_id')->unique()->toArray();
        $allFollowUpIds = $allFollowUps->pluck('id')->toArray();
        $remainingFollowUpIds = array_diff($allFollowUpIds, $sentFollowUpIds);

        return [
            'total_followups' => $allFollowUps->count(),
            'last_reset_message' => $lastResetMessage,
            'followups_since_reset_count' => $followUpMessagesSinceReset->count(),
            'remaining_followups_count' => count($remainingFollowUpIds),
            'all_followups' => $allFollowUps->toArray(),
            'followup_messages_since_reset' => $followUpMessagesSinceReset->toArray(),
            'sent_followup_ids_since_reset' => $sentFollowUpIds,
            'remaining_followup_ids' => array_values($remainingFollowUpIds),
            'sequence_complete' => empty($remainingFollowUpIds)
        ];
    }

    /**
     * Handle failed job - DON'T automatically mark as followed up
     */
    public function failed(\Throwable $exception): void
    { 

        // Clear processing lock but DON'T mark as followed up
        // This allows the system to retry later if needed
        $this->clearProcessingLock();

        // Only mark as followed up if it was a critical error that won't resolve
        if ($this->isCriticalError($exception)) {
            DB::table('history_chat_details')
                ->where('id', $this->chatData->last_message_id)
                ->update([
                    'is_follow_up' => 'yes',
                    'updated_at' => now()
                ]);
 
        }
    }

    /**
     * Determine if error is critical and won't resolve with retry
     */
    private function isCriticalError(\Throwable $exception): bool
    {
        $criticalErrors = [
            'Chat no longer exists',
            'Invalid follow-up configuration',
            'Permanent API key error'
        ];

        foreach ($criticalErrors as $criticalError) {
            if (strpos($exception->getMessage(), $criticalError) !== false) {
                return true;
            }
        }

        return false;
    }

    // ... rest of the methods remain the same as in original file ...
    // (canSendFollowUp, generateFollowUpResponse, createFollowUpMessage, etc.)

    private function canSendFollowUp(): bool
    {
        if (!$this->chatData->merchant_id) {
            return true;
        }

        $cacheKey = "merchant_credit_{$this->chatData->merchant_id}";

        return Cache::remember($cacheKey, 60, function () {
            $business = Setting::where('id', $this->chatData->merchant_id)
                ->with([
                    'package_active_topup:business_id,ai_response,using_credit_limit',
                    'package_active:business_id,ai_response,using_credit_limit'
                ])
                ->first();

            if (!$business) {
                return false;
            }

            $topupCredit = $business->package_active_topup?->sisa_credit ?? 0;
            $packageCredit = $business->package_active?->sisa_credit ?? 0;

            $hasCredit = $topupCredit > 0 || $packageCredit > 0;
 

            return $hasCredit;
        });
    }

    private function generateFollowUpResponse($followUpData): ?string
    {
        if ($followUpData->exact === 'yes') { 
            return $followUpData->text;
        }

        return $this->generateAiResponse($followUpData);
    }

    private function generateAiResponse($followUpData): ?string
    {
        try {
            $aiSettings = $this->getAiSettings();
            if (!$aiSettings) { 
                return null;
            }

            $conversations = $this->getConversationHistory(); 
            if ($aiSettings->ai_option === 'chatgpt') {
                return $this->generateOpenAiResponse($aiSettings->open_ai_key, $followUpData, $conversations);
            } elseif ($aiSettings->ai_option === 'gemini') {
                return $this->generateGeminiResponse($aiSettings->open_ai_key, $followUpData->text, $conversations);
            }
 
            return null;
        } catch (\Exception $e) { 
            return null;
        }
    }

    private function getAiSettings()
    {
        // Per-merchant AI key - use the merchant's own key, not global
        $merchantId = $this->chatData->merchant_id ?? null;
        $cacheKey   = 'ai_settings_' . ($merchantId ?? 'global');

        return Cache::remember($cacheKey, 300, function () use ($merchantId) {
            if ($merchantId) {
                $merchantSettings = DB::table('settings')
                    ->where('merchant_id', $merchantId)
                    ->select(['open_ai_key', 'ai_option'])
                    ->first();
                // Use merchant key if they have one, otherwise fall back to global
                if ($merchantSettings && !empty($merchantSettings->open_ai_key)) {
                    return $merchantSettings;
                }
            }
            // Fallback: global platform AI settings
            return DB::table('settings')
                ->whereNull('merchant_id')
                ->select(['open_ai_key', 'ai_option'])
                ->first();
        });
    }

    private function getConversationHistory()
    {
        $historyLimit = $this->chatData->history_limit ?? 10;

        return DB::table('history_chat_details')
            ->where('history_chat_id', $this->chatData->chat_id)
            ->orderBy('created_at', 'desc')
            ->limit($historyLimit)
            ->select(['from', 'message', 'created_at'])
            ->get()
            ->reverse()
            ->values();
    }

    private function generateOpenAiResponse($apiKey, $followUpData, $conversations): ?string
    {
        try {
            $openAiService = new OpenAiServiceObserver();
            $result = $openAiService->forFollowUp(
                $apiKey,
                $followUpData->text,
                $followUpData->text,
                $conversations,
                $this->chatData->model_ai
            );

            if ($result->status() === 200) {
                $responseBody = json_decode($result->body());
                $content = $responseBody->choices[0]->message->content ?? null;

                if ($content) { 
                }

                return $content;
            }
 
        } catch (\Exception $e) { 
        }

        return null;
    }

    private function generateGeminiResponse($apiKey, $prompt, $conversations): ?string
    {
        try {
            $geminiService = new GeminiAiServiceObserver();
            $result = $geminiService->getQuestionGemini($apiKey, '', $prompt, $conversations);

            if ($result->status() === 200) {
                $responseBody = json_decode($result->body());
                $content = $responseBody->candidates[0]->content->parts[0]->text ?? null;

                if ($content) { 
                }

                return $content;
            }
 
        } catch (\Exception $e) {
            Log::error("Gemini generation failed: " . $e->getMessage());
        }

        return null;
    }

    private function createFollowUpMessage(string $response, $followUpData)
    {
        $isLastFollowUp = $this->isLastFollowUp($followUpData);

        $message = HistoryChatDetail::create([
            'history_chat_id' => $this->chatData->chat_id,
            'from' => 'device',
            'message' => $response,
            'is_follow_up' => $isLastFollowUp ? 'yes' : 'no',
            'follow_up_id' => $followUpData->id,
        ]);
 

        return $message;
    }

    private function isLastFollowUp($followUpData): bool
    {
        $totalFollowUps = DB::table('follow_ups')
            ->where('finetunnel_id', $followUpData->finetunnel_id)
            ->count();

        $sentCount = DB::table('history_chat_details')
            ->where('history_chat_id', $this->chatData->chat_id)
            ->whereNotNull('follow_up_id')
            ->count();

        return $totalFollowUps <= ($sentCount + 1);
    }

    private function markMessageAsFollowedUp(): void
    {
        DB::table('history_chat_details')
            ->where('id', $this->chatData->last_message_id)
            ->update([
                'is_follow_up' => 'yes',
                'updated_at' => now()
            ]);
 
    }

    private function clearFollowUpCache(): void
    {
        Cache::forget("sent_followups_{$this->chatData->chat_id}");
        Cache::forget("merchant_credit_{$this->chatData->merchant_id}");
    }

    private function sendExternalNotifications(string $response, $followUpMessage): void
    {
        try {
            // Get the actual HistoryChatDetail record that was created
            $historyChatDetail = DB::table('history_chat_details')
                ->where('id', $followUpMessage->id)
                ->first();

            if (!$historyChatDetail) {
               
                return;
            }

            $this->sendToExpressServer($followUpMessage);
            $this->sendWhatsAppMessage($response);

          
        } catch (\Exception $e) {
            Log::warning("External notification failed but follow-up was saved", [
                'chat_id' => $this->chatData->chat_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendToExpressServer($messageData): void
    {
        try {
            $response = Http::timeout(10)
                ->retry(2, 1000)
                ->post(
                    config('services.express.url') . '/trigger-whatsapp',
                    HistoryChatResources::make($messageData)
                );

            if ($response->successful()) {
                
            } else {
                
            }
        } catch (\Exception $e) {
            Log::warning("Express server notification exception: " . $e->getMessage());
        }
    }

    private function sendWhatsAppMessage(string $response): void
    {
        if ($this->chatData->chat_from !== 'whatsapp' || !$this->chatData->device_id) {
            return;
        }

        try {
            $whatsappService = new WhatsappServiceObserver();
            $result = $whatsappService->sendMessage(
                $this->chatData->from_number,
                $this->chatData->device_id,
                $response,
                '',
                'description',
                null,
                false
            );

            
        } catch (\Exception $e) {
             
        }
    }
}
