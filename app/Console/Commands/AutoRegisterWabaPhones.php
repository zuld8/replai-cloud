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

        // Find meta_accounts that have NO active whatsapp_key_accounts
        $pendingMetas = MetaAccount::whereDoesntHave('devices', function ($q) {
            $q->where('status', 'active');
        })->whereNotNull('access_token')->get();

        if ($pendingMetas->isEmpty()) {
            $this->info('No pending WABA accounts found.');
            return 0;
        }

        $this->info("Found {$pendingMetas->count()} pending WABA account(s)");

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

        $this->info('Done!');
        return 0;
    }
}
