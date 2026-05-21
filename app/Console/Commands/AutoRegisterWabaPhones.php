<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MetaAccount;
use App\Models\WhatsappKeyAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AutoRegisterWabaPhones extends Command
{
    protected $signature = 'waba:auto-register';
    protected $description = 'Auto-register pending WABA phone numbers that have been approved by Meta but not yet registered in the system';

    public function handle()
    {
        $apiVersion = config('custom.api_waba_version', 'v21.0');

        // ── PHASE 1: MetaAccounts with NO active key_account ─────────────────
        $pendingMetas = MetaAccount::whereDoesntHave('devices', function ($q) {
            $q->where('status', 'active');
        })->whereNotNull('access_token')->get();

        if ($pendingMetas->isNotEmpty()) {
            $this->info("PHASE 1: Found {$pendingMetas->count()} pending WABA account(s)");

            foreach ($pendingMetas as $meta) {
                try {
                    $this->info("Processing: {$meta->name} ({$meta->id})");

                    $wabaId = $meta->business_app;
                    if (!$wabaId) {
                        $this->warn("  No WABA ID (business_app) - skipping");
                        continue;
                    }

                    // Check WABA account review status
                    $wabaResponse = Http::withToken($meta->access_token)
                        ->get("https://graph.facebook.com/{$apiVersion}/{$wabaId}", [
                            'fields' => 'name,account_review_status'
                        ]);

                    if (!$wabaResponse->successful()) {
                        $this->warn("  API error: " . $wabaResponse->body());
                        continue;
                    }

                    $reviewStatus = $wabaResponse->json('account_review_status');
                    if ($reviewStatus !== 'APPROVED') {
                        $this->warn("  WABA not approved yet (status: {$reviewStatus}) - skipping");
                        continue;
                    }

                    // Get phone numbers
                    $phonesResponse = Http::withToken($meta->access_token)
                        ->get("https://graph.facebook.com/{$apiVersion}/{$wabaId}/phone_numbers");

                    if (!$phonesResponse->successful()) {
                        $this->warn("  Failed to get phone numbers");
                        continue;
                    }

                    $phones = $phonesResponse->json('data', []);
                    if (empty($phones)) {
                        $this->warn("  No phone numbers found");
                        continue;
                    }

                    foreach ($phones as $phoneData) {
                        $phoneNumberId = $phoneData['id'];
                        $displayPhone = $phoneData['display_phone_number'] ?? '';
                        $verifiedName = $phoneData['verified_name'] ?? '';
                        $cleanedPhone = preg_replace('/[^0-9]/', '', $displayPhone);

                        // Check if already registered
                        $existing = WhatsappKeyAccount::where('phone', $cleanedPhone)->first();
                        if ($existing && $existing->status === 'active') {
                            $this->info("  Phone {$displayPhone} already active - skipping");
                            continue;
                        }

                        // Register phone number with Meta
                        $regResponse = Http::withToken($meta->access_token)
                            ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/register", [
                                'messaging_product' => 'whatsapp',
                                'pin' => '123456'
                            ]);

                        if (!$regResponse->successful() && !str_contains($regResponse->body(), 'already registered')) {
                            $this->warn("  Failed to register {$displayPhone}: " . $regResponse->body());
                            continue;
                        }

                        // Get phone status for metadata
                        $statusResponse = Http::withToken($meta->access_token)
                            ->get("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}", [
                                'fields' => 'display_phone_number,verified_name,quality_rating,name_status,status'
                            ]);

                        $phoneStatus = $statusResponse->successful() ? $statusResponse->json() : [];

                        // Create/update key account
                        $metadataArray = [
                            'whatsapp' => [
                                'is_embedded_signup' => 1,
                                'access_token' => $meta->access_token,
                                'phone_number_id' => $phoneNumberId,
                                'waba_id' => $wabaId,
                                'display_phone_number' => $displayPhone,
                                'verified_name' => $verifiedName,
                                'quality_rating' => $phoneStatus['quality_rating'] ?? 'GREEN',
                                'name_status' => $phoneStatus['name_status'] ?? 'APPROVED',
                                'status' => $phoneStatus['status'] ?? 'CONNECTED',
                                'webhook_subscribed' => true,
                            ]
                        ];

                        $device = WhatsappKeyAccount::updateOrCreate(
                            ['phone' => $cleanedPhone],
                            [
                                'meta_data' => json_encode($metadataArray),
                                'callback_token' => $wabaId,
                                'status' => 'active',
                                'meta_account_id' => $meta->id,
                                'business_id' => $meta->business_id,
                            ]
                        );

                        // Register webhook override
                        $tokenUid = DB::table('settings')->value('id');
                        $webhookUrl = config('app.url') . '/api-app/waba/callback-url/' . $tokenUid;

                        Http::withToken($meta->access_token)
                            ->post("https://graph.facebook.com/{$apiVersion}/{$wabaId}/subscribed_apps", [
                                'override_callback_uri' => $webhookUrl,
                                'verify_token' => $tokenUid,
                            ]);

                        $this->info("  ✅ Registered: {$displayPhone} ({$verifiedName}) -> device {$device->id}");
                        Log::info("WABA Auto-Register: {$displayPhone} for {$meta->name} -> device {$device->id}");
                    }

                } catch (\Exception $e) {
                    $this->error("  Error: " . $e->getMessage());
                    Log::error("WABA Auto-Register error for {$meta->name}: " . $e->getMessage());
                }
            }
        } else {
            $this->info('PHASE 1: No new pending WABA accounts.');
        }

        // ── PHASE 2: Existing embedded-signup key_accounts stuck at PENDING ──
        // Find key_accounts where meta_data.whatsapp.is_embedded_signup = 1
        // and Meta API still reports status PENDING
        $this->info("PHASE 2: Checking embedded-signup accounts for PENDING status at Meta...");

        $embeddedDevices = WhatsappKeyAccount::where('status', 'active')
            ->whereRaw("JSON_EXTRACT(meta_data, '$.whatsapp.is_embedded_signup') = 1")
            ->whereRaw("JSON_EXTRACT(meta_data, '$.whatsapp.phone_number_id') IS NOT NULL")
            ->whereRaw("JSON_EXTRACT(meta_data, '$.whatsapp.access_token') IS NOT NULL")
            ->get();

        $pendingCount = 0;

        foreach ($embeddedDevices as $device) {
            try {
                $metaData = json_decode($device->meta_data, true);
                $wa = $metaData['whatsapp'] ?? [];
                $phoneNumberId = $wa['phone_number_id'] ?? null;
                $accessToken   = $wa['access_token'] ?? null;
                $wabaId        = $wa['waba_id'] ?? null;

                if (!$phoneNumberId || !$accessToken) continue;

                // Query Meta API for current phone status
                $statusResp = Http::withToken($accessToken)
                    ->get("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}", [
                        'fields' => 'display_phone_number,status,code_verification_status,quality_rating,name_status'
                    ]);

                if (!$statusResp->successful()) continue;

                $currentStatus = $statusResp->json('status');

                // Only act if phone is still PENDING
                if ($currentStatus !== 'PENDING') continue;
                
                // Skip if currently rate-limited
                $rateLimitUntil = $keyAccount->rate_limit_until ?? null;
                if ($rateLimitUntil && now()->lessThan($rateLimitUntil)) {
                    $this->info("  Skipping {$displayPhone} — rate limited until {$rateLimitUntil}");
                    continue;
                }

                $pendingCount++;
                $displayPhone = $wa['display_phone_number'] ?? $device->phone;
                $this->warn("  Phone {$displayPhone} still PENDING at Meta — re-registering...");

                // Re-call /register
                $regResp = Http::withToken($accessToken)
                    ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/register", [
                        'messaging_product' => 'whatsapp',
                        'pin' => '123456'
                    ]);

                if (!$regResp->successful() && !str_contains($regResp->body(), 'already registered')) {
                    $this->warn("  Re-register failed: " . $regResp->body());
                    $regBody = json_decode($regResp->body(), true);
                    $errCode = $regBody['error']['code'] ?? 0;
                    Log::warning("WABA PENDING re-register failed for {$displayPhone}: " . $regResp->body());
                    
                    // If rate limited (#133016), mark phone to skip for 6 hours
                    if ($errCode == 133016) {
                        Log::warning("WABA rate limited for {$displayPhone} — skipping for 6 hours");
                        \App\Models\WhatsappKeyAccount::where('phone', preg_replace('/[^0-9]/', '', $displayPhone))
                            ->whereNotNull('meta_data')
                            ->update(['rate_limit_until' => now()->addHours(6)]);
                        continue;
                    }
                    continue;
                }

                // Re-check status after register
                sleep(2);
                $newStatusResp = Http::withToken($accessToken)
                    ->get("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}", [
                        'fields' => 'status,quality_rating,name_status'
                    ]);

                $newStatus = $newStatusResp->successful() ? ($newStatusResp->json('status') ?? 'CONNECTED') : 'CONNECTED';

                // Update meta_data with new status
                $metaData['whatsapp']['status'] = $newStatus;
                $device->update(['meta_data' => json_encode($metaData)]);

                // Re-subscribe webhook
                if ($wabaId) {
                    $tokenUid = DB::table('settings')->value('id');
                    $webhookUrl = config('app.url') . '/api-app/waba/callback-url/' . $tokenUid;
                    Http::withToken($accessToken)
                        ->post("https://graph.facebook.com/{$apiVersion}/{$wabaId}/subscribed_apps", [
                            'override_callback_uri' => $webhookUrl,
                            'verify_token' => $tokenUid,
                        ]);
                }

                $this->info("  ✅ Re-registered: {$displayPhone} → new status: {$newStatus}");
                Log::info("WABA PENDING fix: {$displayPhone} (device {$device->id}) → {$newStatus}");

            } catch (\Exception $e) {
                $this->error("  Phase 2 error for device {$device->id}: " . $e->getMessage());
                Log::error("WABA Phase2 error device {$device->id}: " . $e->getMessage());
            }
        }

        if ($pendingCount === 0) {
            $this->info("PHASE 2: All embedded-signup phones are CONNECTED. Nothing to fix.");
        }

        $this->info('Done!');
        return 0;
    }
}
