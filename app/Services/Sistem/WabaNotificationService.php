<?php

namespace App\Services\Sistem;

use App\Models\WhatsappKeyAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WabaNotificationService
{
    /*
    |--------------------------------------------------------------------------
    | WABA Notification Service
    | Sends transactional WhatsApp messages via Meta Cloud API.
    | Does NOT log to history_chats (by design — system notifications only).
    |--------------------------------------------------------------------------
    */

    protected string $apiVersion;

    public function __construct()
    {
        $this->apiVersion = config('custom.api_waba_version', 'v21.0');
    }

    /**
     * Send a plain text message via WABA (Meta Cloud API).
     * No CRM logging — purely transactional.
     *
     * @param  string  $toPhone     Recipient phone (numbers only, e.g. 628123456789)
     * @param  string  $message     The text to send
     * @param  WhatsappKeyAccount  $wabaDevice  WABA device to use
     * @return bool
     */
    public function sendText(string $toPhone, string $message, WhatsappKeyAccount $wabaDevice): bool
    {
        try {
            $meta = $wabaDevice->metaAccount;
            if (!$meta || !$meta->access_token) {
                Log::warning("[WabaNotif] No access_token for device {$wabaDevice->id}");
                return false;
            }

            $metaData  = json_decode($wabaDevice->meta_data, true);
            $phoneId   = $metaData['whatsapp']['phone_number_id'] ?? null;
            $token     = $meta->access_token;

            if (!$phoneId) {
                Log::warning("[WabaNotif] No phone_number_id for device {$wabaDevice->id}");
                return false;
            }

            // Normalize phone number
            $toPhone = preg_replace('/[^0-9]/', '', $toPhone);
            if (str_starts_with($toPhone, '0')) {
                $toPhone = '62' . substr($toPhone, 1);
            }

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'               => $toPhone,
                    'type'             => 'text',
                    'text'             => ['body' => $message, 'preview_url' => false],
                ]);

            if ($response->successful()) {
                Log::info("[WabaNotif] Sent to {$toPhone} via {$wabaDevice->phone}");
                return true;
            }

            Log::warning("[WabaNotif] Failed to {$toPhone}: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("[WabaNotif] Exception: " . $e->getMessage());
            return false;
        }
    }
}
