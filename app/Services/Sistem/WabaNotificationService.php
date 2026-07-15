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
    public function sendText(string $toPhone, string $message, WhatsappKeyAccount $wabaDevice, ?string $bsuid = null): bool
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

            // Build recipient field: phone → 'to', BSUID → 'recipient'
            $cleanPhone = preg_replace('/[^0-9]/', '', $toPhone);
            if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            if (!empty($cleanPhone)) {
                $recipientField = ['to' => $cleanPhone];
            } elseif (!empty($bsuid)) {
                $recipientField = ['recipient' => $bsuid];
            } else {
                Log::warning("[WabaNotif] No phone or BSUID — skipping");
                return false;
            }

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$phoneId}/messages", array_merge([
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'type'             => 'text',
                    'text'             => ['body' => $message, 'preview_url' => false],
                ], $recipientField));

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
    /**
     * Kirim pesan interaktif (tombol/list) via WABA — Menu Otomatis.
     *
     * @param  string               $toPhone    Nomor penerima
     * @param  \App\Models\ChatFlow\ChatFlowNode $node    Node yang dikirim
     * @param  iterable             $options    ChatFlowOption collection
     * @param  WhatsappKeyAccount   $wabaDevice WABA device
     * @param  string|null          $bsuid      BSUID penerima (jika ada)
     * @return bool
     */
    public function sendInteractive(string $toPhone, $node, $options, WhatsappKeyAccount $wabaDevice, ?string $bsuid = null): bool
    {
        try {
            $meta = $wabaDevice->metaAccount;
            if (!$meta || !$meta->access_token) return false;

            $metaData = json_decode($wabaDevice->meta_data, true);
            $phoneId  = $metaData['whatsapp']['phone_number_id'] ?? null;
            $token    = $meta->access_token;
            if (!$phoneId) return false;

            // Resolve recipient (nomor atau BSUID)
            $cleanPhone = preg_replace('/[^0-9]/', '', $toPhone);
            if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $recipient = !empty($cleanPhone)
                ? ['to' => $cleanPhone]
                : (!empty($bsuid) ? ['recipient' => $bsuid] : null);
            if (!$recipient) return false;

            // Build interactive payload sesuai node type
            if ($node->type === 'buttons') {
                // Maks 3 tombol, label ≤20 char (batas WhatsApp)
                $buttons = collect($options)->take(3)->map(fn($o) => [
                    'type'  => 'reply',
                    'reply' => [
                        'id'    => $o->reply_id,
                        'title' => mb_substr($o->label, 0, 20),
                    ],
                ])->values()->all();

                $interactive = [
                    'type'   => 'button',
                    'body'   => ['text' => $node->body_text],
                    'action' => ['buttons' => $buttons],
                ];

            } elseif ($node->type === 'list') {
                // Maks 10 row, title ≤24, desc ≤72 (batas WhatsApp)
                $rows = collect($options)->take(10)->map(fn($o) => array_filter([
                    'id'          => $o->reply_id,
                    'title'       => mb_substr($o->label, 0, 24),
                    'description' => $o->description ? mb_substr($o->description, 0, 72) : null,
                ]))->values()->all();

                $interactive = [
                    'type'   => 'list',
                    'body'   => ['text' => $node->body_text],
                    'action' => [
                        'button'   => mb_substr($node->list_button_label ?: 'Pilih', 0, 20),
                        'sections' => [['title' => 'Menu', 'rows' => $rows]],
                    ],
                ];

            } else {
                return false; // Node type tidak didukung
            }

            // Header & footer opsional
            if ($node->header) $interactive['header'] = ['type' => 'text', 'text' => mb_substr($node->header, 0, 60)];
            if ($node->footer) $interactive['footer'] = ['text' => mb_substr($node->footer, 0, 60)];

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$phoneId}/messages", array_merge(
                    ['messaging_product' => 'whatsapp', 'recipient_type' => 'individual', 'type' => 'interactive', 'interactive' => $interactive],
                    $recipient
                ));

            if (!$response->successful()) {
                Log::warning('[WabaNotif] sendInteractive failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'device' => $wabaDevice->id,
                ]);
            }

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('[WabaNotif] sendInteractive exception: ' . $e->getMessage());
            return false;
        }
    }

}