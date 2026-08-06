<?php
namespace App\Services\Meta;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MetaCapi — kirim event ke Meta Conversions API (server-side).
 * Fire-and-forget: timeout 3s, try/catch → TIDAK pernah block sistem.
 */
class MetaCapi
{
    /**
     * Kirim event ke CAPI.
     *
     * @param string      $eventName   Nama event Meta (CompleteRegistration, Purchase, dll)
     * @param array       $userData    Data user mentah: ['email'=>..., 'phone'=>...]
     * @param array       $customData  Data tambahan: ['value'=>..., 'currency'=>'IDR']
     * @param string|null $eventId     ID untuk dedup dengan browser pixel (WAJIB untuk konversi)
     * @param string|null $sourceUrl   URL sumber event (default: URL saat ini)
     */
    public static function send(
        string $eventName,
        array  $userData,
        array  $customData = [],
        ?string $eventId   = null,
        ?string $sourceUrl = null
    ): void {
        $pixel = config('services.meta.pixel_id');
        $token = config('services.meta.capi_token');

        // Jika credential belum dikonfigurasi, skip diam-diam
        if (!$pixel || !$token) {
            return;
        }

        // Hash SHA256 (lowercase + trim) — wajib untuk user matching Meta
        $hash = fn($v) => $v ? hash('sha256', strtolower(trim($v))) : null;

        // Bersihkan nomor telpon (hanya angka) lalu hash
        $phoneClean = isset($userData['phone'])
            ? preg_replace('/\D/', '', $userData['phone'])
            : null;

        // Bangun user_data, buang yang null
        $ud = array_filter([
            'em'                => isset($userData['email']) ? [$hash($userData['email'])] : null,
            'ph'                => $phoneClean ? [hash('sha256', $phoneClean)] : null,
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->userAgent(),
            'fbp'               => request()->cookie('_fbp'),
            'fbc'               => request()->cookie('_fbc'),
        ]);

        // Bangun payload event
        $eventPayload = array_filter([
            'event_name'       => $eventName,
            'event_time'       => time(),
            'action_source'    => 'website',
            'event_id'         => $eventId,
            'event_source_url' => $sourceUrl ?? url()->current(),
            'user_data'        => $ud ?: null,
            'custom_data'      => !empty($customData) ? $customData : null,
        ]);

        $payload = ['data' => [$eventPayload]];

        try {
            Http::timeout(3)->post(
                "https://graph.facebook.com/v21.0/{$pixel}/events?access_token={$token}",
                $payload
            );
        } catch (\Throwable $e) {
            // Catat warning tapi JANGAN throw — jangan sampai block sistem
            Log::warning('MetaCAPI gagal: ' . $e->getMessage(), [
                'event' => $eventName,
                'event_id' => $eventId,
            ]);
        }
    }
}
